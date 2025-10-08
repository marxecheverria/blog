/**
 * Sistema de Gestión de Documentos - JavaScript Simple
 */

// Variables globales
const API_URL = 'api.php';
const AUTH_URL = 'auth.php';

let quill = null;
let currentDocument = null;
let allDocuments = [];
let currentUser = null;

// Inicialización
document.addEventListener('DOMContentLoaded', async () => {
    try {
        await checkAuth();
        initializeApp();
        loadDocuments();
        loadStats();
    } catch (error) {
        console.error('Error inicializando:', error);
        showToast('Error al cargar la aplicación', 'error');
    }
});

// Verificar autenticación
async function checkAuth() {
    try {
        const response = await fetch(`${AUTH_URL}?action=check`);
        const data = await response.json();
        
        if (!data.authenticated) {
            window.location.href = 'login.html';
            return;
        }
        
        currentUser = data.user;
        updateUserInfo();
    } catch (error) {
        console.error('Error verificando auth:', error);
        window.location.href = 'login.html';
    }
}

// Actualizar información del usuario
function updateUserInfo() {
    if (!currentUser) return;
    
    document.getElementById('userName').textContent = currentUser.nombre;
    document.getElementById('userEmail').textContent = currentUser.email;
}

// Inicializar aplicación
function initializeApp() {
    // Inicializar Quill
    quill = new Quill('#quillEditor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image'],
                ['clean']
            ]
        },
        placeholder: 'Comienza a escribir tu documento...'
    });

    // Event listeners
    setupEventListeners();
    
    // Mostrar vista por defecto
    showView('documents');
}

// Configurar event listeners
function setupEventListeners() {
    // Navegación
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const view = link.dataset.view;
            if (view) {
                showView(view);
            }
        });
    });

    // Botones del editor
    document.getElementById('saveBtn').addEventListener('click', saveDocument);
    document.getElementById('deleteBtn').addEventListener('click', confirmDelete);
    document.getElementById('closeEditorBtn').addEventListener('click', closeEditor);
    
    // Búsqueda
    document.getElementById('searchInput').addEventListener('input', performSearch);
    document.getElementById('quickSearch').addEventListener('input', quickSearch);
    
    // Logout
    document.getElementById('logoutBtn').addEventListener('click', logout);
    
    // Tema
    document.getElementById('themeToggle').addEventListener('click', toggleTheme);
    
    // Modal
    document.getElementById('modalClose').addEventListener('click', closeModal);
    document.getElementById('modalCancel').addEventListener('click', closeModal);
    document.getElementById('modalConfirm').addEventListener('click', executeModalAction);
    
    // Quill editor
    quill.on('text-change', updateWordCount);
    
    // Aplicar tema guardado
    applySavedTheme();
}

// Mostrar vista
function showView(viewName) {
    console.log('Mostrando vista:', viewName);
    
    // Ocultar todas las vistas
    document.querySelectorAll('.view').forEach(view => {
        view.classList.remove('active');
    });
    
    // Mostrar vista seleccionada
    const targetView = document.getElementById(`view${viewName.charAt(0).toUpperCase() + viewName.slice(1)}`);
    if (targetView) {
        targetView.classList.add('active');
    }
    
    // Actualizar navegación
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.dataset.view === viewName) {
            link.classList.add('active');
        }
    });
    
    // Actualizar título
    const titles = {
        'documents': 'Mis Documentos',
        'new': currentDocument ? 'Editar Documento' : 'Nuevo Documento',
        'search': 'Buscar',
        'stats': 'Estadísticas'
    };
    
    document.getElementById('pageTitle').textContent = titles[viewName] || 'Sistema';
    
    // Limpiar editor solo si es nuevo documento (no edición)
    if (viewName === 'new' && !currentDocument) {
        document.getElementById('docTitle').value = '';
        quill.root.innerHTML = '';
        updateWordCount();
    }
}

// Crear nuevo documento
function createNewDocument() {
    currentDocument = null;
    showView('new');
}

// Cargar documentos
async function loadDocuments() {
    try {
        const response = await fetch(`${API_URL}?action=listar`);
        const data = await response.json();
        
        if (data.success) {
            allDocuments = data.documentos;
            displayDocuments();
            updateDocCount();
        } else {
            showToast('Error al cargar documentos', 'error');
        }
    } catch (error) {
        console.error('Error cargando documentos:', error);
        showToast('Error de conexión', 'error');
    }
}

// Mostrar documentos
function displayDocuments() {
    const container = document.getElementById('documentsGrid');
    
    if (allDocuments.length === 0) {
        container.innerHTML = `
            <div class="loading">
                <i class="fas fa-folder-open"></i>
                <p>No hay documentos</p>
                <button class="btn btn-primary" onclick="createNewDocument()" style="margin-top: 20px;">
                    <i class="fas fa-plus"></i> Crear primer documento
                </button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = allDocuments.map(doc => createDocumentCard(doc)).join('');
}

// Crear tarjeta de documento
function createDocumentCard(doc) {
    const preview = stripHtml(doc.contenido).substring(0, 150);
    const date = formatDate(doc.fecha_modificacion);
    
    return `
        <div class="document-card" onclick="openDocument(${doc.id_documento})">
            <div class="document-header">
                <div class="document-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="document-info">
                    <h3>${escapeHtml(doc.titulo)}</h3>
                    <p>${date}</p>
                </div>
            </div>
            <div class="document-preview">${preview}...</div>
            <div class="document-footer">
                <div class="document-stats">
                    <span><i class="fas fa-font"></i> ${doc.palabras || 0}</span>
                    <span><i class="fas fa-eye"></i> ${doc.vistas || 0}</span>
                </div>
                <div class="document-actions">
                    <button class="action-btn" onclick="event.stopPropagation(); openDocument(${doc.id_documento})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn" onclick="event.stopPropagation(); confirmDeleteDocument(${doc.id_documento})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Abrir documento
async function openDocument(id) {
    try {
        const response = await fetch(`${API_URL}?action=ver&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            currentDocument = data.documento;
            document.getElementById('docTitle').value = currentDocument.titulo;
            quill.root.innerHTML = currentDocument.contenido || '';
            updateWordCount();
            
            // Mostrar vista de editor pero con título correcto
            showView('new');
            document.getElementById('pageTitle').textContent = 'Editar Documento';
            
            console.log('Documento cargado para edición:', currentDocument.titulo);
        } else {
            showToast('Error al cargar documento', 'error');
        }
    } catch (error) {
        console.error('Error abriendo documento:', error);
        showToast('Error de conexión', 'error');
    }
}

// Guardar documento
async function saveDocument() {
    const title = document.getElementById('docTitle').value.trim();
    const content = quill.root.innerHTML;
    
    if (!title) {
        showToast('Ingresa un título', 'warning');
        return;
    }
    
    const isNew = !currentDocument || !currentDocument.id_documento;
    const url = `${API_URL}?action=${isNew ? 'crear' : 'editar'}`;
    const body = {
        titulo: title,
        contenido: content,
        palabras: quill.getText().trim().split(/\s+/).length,
        caracteres: quill.getText().length
    };
    
    if (!isNew) {
        body.id_documento = currentDocument.id_documento;
    }
    
    try {
        const response = await fetch(url, {
            method: isNew ? 'POST' : 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(isNew ? 'Documento creado' : 'Documento actualizado', 'success');
            
            if (isNew) {
                currentDocument = {
                    id_documento: data.id_documento,
                    titulo: title,
                    contenido: content
                };
            }
            
            await loadDocuments();
            await loadStats();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error guardando:', error);
        showToast('Error de conexión', 'error');
    }
}

// Confirmar eliminación
function confirmDelete() {
    if (!currentDocument || !currentDocument.id_documento) {
        showToast('No hay documento para eliminar', 'warning');
        return;
    }
    
    showModal(
        'Eliminar documento',
        `¿Eliminar "${currentDocument.titulo}"? Esta acción no se puede deshacer.`,
        () => deleteDocument(currentDocument.id_documento)
    );
}

// Confirmar eliminación de documento específico
function confirmDeleteDocument(id) {
    const doc = allDocuments.find(d => d.id_documento === id);
    if (!doc) return;
    
    showModal(
        'Eliminar documento',
        `¿Eliminar "${doc.titulo}"? Esta acción no se puede deshacer.`,
        () => deleteDocument(id)
    );
}

// Eliminar documento
async function deleteDocument(id) {
    try {
        const response = await fetch(`${API_URL}?action=eliminar&id=${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Documento eliminado', 'success');
            closeModal();
            
            if (currentDocument && currentDocument.id_documento === id) {
                showView('documents');
            }
            
            await loadDocuments();
            await loadStats();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error eliminando:', error);
        showToast('Error de conexión', 'error');
    }
}

// Actualizar contador de palabras
function updateWordCount() {
    const text = quill.getText().trim();
    const words = text ? text.split(/\s+/).length : 0;
    const chars = text.length;
    
    document.getElementById('wordCount').textContent = `${words} palabras`;
    document.getElementById('charCount').textContent = `${chars} caracteres`;
}

// Actualizar contador de documentos
function updateDocCount() {
    document.getElementById('docCount').textContent = allDocuments.length;
}

// Cargar estadísticas
async function loadStats() {
    try {
        const response = await fetch(`${AUTH_URL}?action=profile`);
        const data = await response.json();
        
        if (data.success) {
            const stats = data.perfil;
            document.getElementById('totalDocs').textContent = stats.total_documentos || 0;
            document.getElementById('totalWords').textContent = formatNumber(stats.total_palabras || 0);
            document.getElementById('totalViews').textContent = stats.total_vistas || 0;
        }
    } catch (error) {
        console.error('Error cargando stats:', error);
    }
}

// Búsqueda
function performSearch() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const results = document.getElementById('searchResults');
    
    if (!query) {
        results.innerHTML = '';
        return;
    }
    
    const filtered = allDocuments.filter(doc => 
        doc.titulo.toLowerCase().includes(query) ||
        stripHtml(doc.contenido).toLowerCase().includes(query)
    );
    
    if (filtered.length === 0) {
        results.innerHTML = '<p style="text-align: center; color: var(--text-secondary);">No se encontraron resultados</p>';
        return;
    }
    
    results.innerHTML = `<div class="documents-grid">${filtered.map(doc => createDocumentCard(doc)).join('')}</div>`;
}

// Búsqueda rápida
function quickSearch() {
    const query = document.getElementById('quickSearch').value;
    if (query) {
        document.getElementById('searchInput').value = query;
        showView('search');
        performSearch();
    }
}

// Cerrar sesión
async function logout() {
    try {
        await fetch(`${AUTH_URL}?action=logout`, { method: 'POST' });
        window.location.href = 'login.html';
    } catch (error) {
        console.error('Error cerrando sesión:', error);
        window.location.href = 'login.html';
    }
}

// Toggle tema oscuro/claro
function toggleTheme() {
    const body = document.body;
    const isDark = body.classList.contains('dark-theme');
    
    if (isDark) {
        body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
        updateThemeIcon('light');
    } else {
        body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
        updateThemeIcon('dark');
    }
    
    // Guardar preferencia en el servidor
    saveThemePreference(!isDark ? 'dark' : 'light');
}

// Aplicar tema guardado
function applySavedTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const body = document.body;
    
    if (savedTheme === 'dark') {
        body.classList.add('dark-theme');
    } else {
        body.classList.remove('dark-theme');
    }
    
    updateThemeIcon(savedTheme);
}

// Actualizar icono del tema
function updateThemeIcon(theme) {
    const icon = document.querySelector('#themeToggle i');
    if (icon) {
        if (theme === 'dark') {
            icon.className = 'fas fa-sun';
        } else {
            icon.className = 'fas fa-moon';
        }
    }
}

// Guardar preferencia de tema en el servidor
async function saveThemePreference(theme) {
    try {
        await fetch(`${AUTH_URL}?action=update_theme`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ tema: theme })
        });
    } catch (error) {
        console.error('Error guardando tema:', error);
    }
}

// Modal
let modalAction = null;

function showModal(title, message, action) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    modalAction = action;
    document.getElementById('confirmModal').classList.add('show');
}

function closeModal() {
    document.getElementById('confirmModal').classList.remove('show');
    modalAction = null;
}

function executeModalAction() {
    if (modalAction) {
        modalAction();
    }
}

// Toast
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    const messageEl = document.getElementById('toastMessage');
    
    messageEl.textContent = message;
    toast.className = `toast ${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Utilidades
function stripHtml(html) {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
}

// Cerrar editor
function closeEditor() {
    // Verificar si hay cambios sin guardar
    const title = document.getElementById('docTitle').value.trim();
    const content = quill.root.innerHTML;
    
    // Si hay contenido sin guardar, preguntar
    if ((title || content !== '<p><br></p>') && !currentDocument) {
        showModal(
            '¿Cerrar sin guardar?',
            'Tienes cambios sin guardar. ¿Estás seguro de que deseas cerrar el editor?',
            () => {
                // Confirmar: cerrar y volver a documentos
                currentDocument = null;
                showView('documents');
                closeModal();
            }
        );
    } else {
        // No hay cambios o ya está guardado, cerrar directamente
        currentDocument = null;
        showView('documents');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'Hace un momento';
    if (diff < 3600000) return `Hace ${Math.floor(diff / 60000)} min`;
    if (diff < 86400000) return `Hace ${Math.floor(diff / 3600000)} h`;
    if (diff < 604800000) return `Hace ${Math.floor(diff / 86400000)} días`;
    
    return date.toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function formatNumber(num) {
    return new Intl.NumberFormat('es-ES').format(num);
}

// Atajos de teclado
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        saveDocument();
    }
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        createNewDocument();
    }
    if (e.key === 'Escape') {
        // Si hay un modal abierto, cerrarlo
        const modal = document.getElementById('confirmModal');
        if (modal && modal.classList.contains('show')) {
            closeModal();
        } else {
            // Si no hay modal y estamos en el editor, cerrar el editor
            const viewNew = document.getElementById('viewNew');
            if (viewNew && viewNew.classList.contains('active')) {
                closeEditor();
            }
        }
    }
});
