<?php
/**
 * Sistema Atlas - Controlador de Equipos
 * 
 * Maneja la gestión de equipos electrónicos
 * 
 * @package Atlas\Controllers
 * @version 1.0
 */

namespace Atlas\Controllers;

use Atlas\Core\Controller;
use Atlas\Core\Auth;
use Atlas\Core\Middleware;
use Atlas\Core\QRCodeGenerator;
use Atlas\Models\Equipo;
use Atlas\Models\ImagenEquipo;
use Atlas\Models\CodigoQR;

class EquipoController extends Controller
{
    /**
     * Modelo de Equipo
     * @var Equipo
     */
    private Equipo $equipoModel;

    /**
     * Modelo de ImagenEquipo
     * @var ImagenEquipo
     */
    private ImagenEquipo $imagenModel;

    /**
     * Modelo de CodigoQR
     * @var CodigoQR
     */
    private CodigoQR $qrModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->equipoModel = new Equipo();
        $this->imagenModel = new ImagenEquipo();
        $this->qrModel = new CodigoQR();
    }

    /**
     * Muestra el listado de equipos
     * 
     * @return void
     */
    public function index(): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.leer');

        $user = Auth::user();
        $id_usuario = $user['id_usuario'];
        
        // Obtener filtros
        $filters = [
            'marca' => $_GET['marca'] ?? '',
            'modelo' => $_GET['modelo'] ?? '',
            'numero_serie' => $_GET['numero_serie'] ?? '',
            'estado_equipo' => $_GET['estado_equipo'] ?? ''
        ];

        // Si es admin, mostrar todos los equipos, sino solo los del usuario
        if (in_array($user['nombre_rol'], ['Administrador', 'Administrativo'])) {
            $equipos = $this->equipoModel->search($filters);
            $stats = null;
        } else {
            $equipos = $this->equipoModel->search($filters, $id_usuario);
            $stats = $this->equipoModel->getStatsForUser($id_usuario);
        }

        $this->render('equipos/index', [
            'title' => 'Mis Equipos',
            'styles' => ['equipos.css'],
            'user' => $user,
            'equipos' => $equipos,
            'stats' => $stats
        ]);
    }

    /**
     * Muestra un equipo específico
     * 
     * @param int $id ID del equipo
     * @return void
     */
    public function show(int $id): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.leer');

        $user = Auth::user();
        $equipo = $this->equipoModel->getWithDetails($id);

        if (!$equipo) {
            $_SESSION['error_message'] = 'Equipo no encontrado';
            $this->redirect('/equipos');
            return;
        }

        // Verificar que el usuario puede ver este equipo
        $es_propietario = $equipo['id_usuario'] == $user['id_usuario'];
        $es_admin = in_array($user['nombre_rol'], ['Administrador', 'Administrativo']);

        if (!$es_propietario && !$es_admin) {
            $_SESSION['error_message'] = 'No tienes permiso para ver este equipo';
            $this->redirect('/equipos');
            return;
        }

        // Obtener imágenes del equipo
        $imagenes = $this->imagenModel->getByEquipo($id);

        // Obtener estado actual
        $estado_actual = $this->equipoModel->getEstadoActual($id);

        $this->render('equipos/show', [
            'title' => 'Detalle del Equipo',
            'styles' => ['equipos-detail.css'],
            'scripts' => [],
            'user' => $user,
            'equipo' => $equipo,
            'imagenes' => $imagenes,
            'estado_actual' => $estado_actual,
            'es_propietario' => $es_propietario,
            'es_admin' => $es_admin
        ]);
    }

    /**
     * Muestra el formulario para crear un equipo
     * 
     * @return void
     */
    public function create(): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.crear');

        $user = Auth::user();

        $this->render('equipos/create', [
            'title' => 'Registrar Equipo',
            'styles' => ['equipos-form.css'],
            'scripts' => ['equipo-form.js'],
            'user' => $user
        ]);
    }

    /**
     * Almacena un nuevo equipo
     * 
     * @return void
     */
    public function store(): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.crear');

        if (!$this->isPost()) {
            $this->redirect('/equipos/crear');
            return;
        }

        $user = Auth::user();

        // Validar datos
        $numero_serie = $this->sanitize($this->post('numero_serie', ''));
        $marca = $this->sanitize($this->post('marca', ''));
        $modelo = $this->sanitize($this->post('modelo', ''));
        $descripcion = $this->sanitize($this->post('descripcion', ''));

        // Validaciones
        if (empty($numero_serie) || empty($marca) || empty($modelo)) {
            $_SESSION['error_message'] = 'Todos los campos obligatorios deben estar completos';
            $this->redirect('/equipos/crear');
            return;
        }

        // Verificar unicidad del número de serie
        if ($this->equipoModel->numeroSerieExists($numero_serie)) {
            $_SESSION['error_message'] = 'El número de serie ya existe en el sistema';
            $this->redirect('/equipos/crear');
            return;
        }

        // Crear el equipo
        $equipo_id = $this->equipoModel->create([
            'id_usuario' => $user['id_usuario'],
            'numero_serie' => $numero_serie,
            'marca' => $marca,
            'modelo' => $modelo,
            'descripcion' => $descripcion,
            'estado_equipo' => 'activo'
        ]);

        if (!$equipo_id) {
            $_SESSION['error_message'] = 'Error al registrar el equipo';
            $this->redirect('/equipos/crear');
            return;
        }

        // Procesar imágenes si hay
        if (!empty($_FILES['imagenes']['name'][0])) {
            $this->uploadImages($equipo_id, $_FILES['imagenes']);
        }

        $_SESSION['success_message'] = 'Equipo registrado exitosamente';
        $this->redirect('/equipos/' . $equipo_id);
    }

    /**
     * Muestra el formulario para editar un equipo
     * 
     * @param int $id ID del equipo
     * @return void
     */
    public function edit(int $id): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.actualizar');

        $user = Auth::user();
        $equipo = $this->equipoModel->getWithDetails($id);

        if (!$equipo) {
            $_SESSION['error_message'] = 'Equipo no encontrado';
            $this->redirect('/equipos');
            return;
        }

        // Verificar que el usuario puede editar este equipo
        $es_propietario = $equipo['id_usuario'] == $user['id_usuario'];
        $es_admin = in_array($user['nombre_rol'], ['Administrador', 'Administrativo']);

        if (!$es_propietario && !$es_admin) {
            $_SESSION['error_message'] = 'No tienes permiso para editar este equipo';
            $this->redirect('/equipos');
            return;
        }

        // Obtener imágenes del equipo
        $imagenes = $this->imagenModel->getByEquipo($id);

        $this->render('equipos/edit', [
            'user' => $user,
            'equipo' => $equipo,
            'imagenes' => $imagenes
        ]);
    }

    /**
     * Actualiza un equipo existente
     * 
     * @param int $id ID del equipo
     * @return void
     */
    public function update(int $id): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.actualizar');

        if (!$this->isPost()) {
            $this->redirect('/equipos/' . $id . '/editar');
            return;
        }

        $user = Auth::user();
        $equipo = $this->equipoModel->find($id);

        if (!$equipo) {
            $_SESSION['error_message'] = 'Equipo no encontrado';
            $this->redirect('/equipos');
            return;
        }

        // Verificar permisos
        $es_propietario = $equipo['id_usuario'] == $user['id_usuario'];
        $es_admin = in_array($user['nombre_rol'], ['Administrador', 'Administrativo']);

        if (!$es_propietario && !$es_admin) {
            $_SESSION['error_message'] = 'No tienes permiso para editar este equipo';
            $this->redirect('/equipos');
            return;
        }

        // Validar datos
        $numero_serie = $this->sanitize($this->post('numero_serie', ''));
        $marca = $this->sanitize($this->post('marca', ''));
        $modelo = $this->sanitize($this->post('modelo', ''));
        $descripcion = $this->sanitize($this->post('descripcion', ''));
        $estado_equipo = $this->post('estado_equipo', 'activo');

        if (empty($numero_serie) || empty($marca) || empty($modelo)) {
            $_SESSION['error_message'] = 'Todos los campos obligatorios deben estar completos';
            $this->redirect('/equipos/' . $id . '/editar');
            return;
        }

        // Verificar unicidad del número de serie
        if ($this->equipoModel->numeroSerieExists($numero_serie, $id)) {
            $_SESSION['error_message'] = 'El número de serie ya existe en el sistema';
            $this->redirect('/equipos/' . $id . '/editar');
            return;
        }

        // Actualizar el equipo
        $updated = $this->equipoModel->update($id, [
            'numero_serie' => $numero_serie,
            'marca' => $marca,
            'modelo' => $modelo,
            'descripcion' => $descripcion,
            'estado_equipo' => $estado_equipo
        ]);

        if (!$updated) {
            $_SESSION['error_message'] = 'Error al actualizar el equipo';
            $this->redirect('/equipos/' . $id . '/editar');
            return;
        }

        // Procesar eliminación de imágenes
        $imagenes_eliminar = $this->post('imagenes_eliminar', '');
        if (!empty($imagenes_eliminar)) {
            $ids = explode(',', $imagenes_eliminar);
            foreach ($ids as $imagen_id) {
                if (is_numeric($imagen_id)) {
                    $imagen = $this->imagenModel->deleteImagen((int)$imagen_id);
                    if ($imagen && file_exists(PUBLIC_PATH . $imagen['ruta_imagen'])) {
                        unlink(PUBLIC_PATH . $imagen['ruta_imagen']);
                    }
                }
            }
        }

        // Establecer nueva imagen principal si se especificó
        $imagen_principal_id = $this->post('imagen_principal_id');
        if (!empty($imagen_principal_id) && is_numeric($imagen_principal_id)) {
            $this->imagenModel->setPrincipal($id, (int)$imagen_principal_id);
        }

        // Procesar nuevas imágenes
        if (!empty($_FILES['imagenes_nuevas']['name'][0])) {
            $this->uploadImages($id, $_FILES['imagenes_nuevas']);
        }

        $_SESSION['success_message'] = 'Equipo actualizado exitosamente';
        $this->redirect('/equipos/' . $id);
    }

    /**
     * Elimina un equipo (soft delete)
     * 
     * @param int $id ID del equipo
     * @return void
     */
    public function delete(int $id): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.eliminar');

        if (!$this->isPost()) {
            $this->redirect('/equipos');
            return;
        }

        $user = Auth::user();
        $equipo = $this->equipoModel->find($id);

        if (!$equipo) {
            $_SESSION['error_message'] = 'Equipo no encontrado';
            $this->redirect('/equipos');
            return;
        }

        // Verificar permisos
        $es_propietario = $equipo['id_usuario'] == $user['id_usuario'];
        $es_admin = in_array($user['nombre_rol'], ['Administrador', 'Administrativo']);

        if (!$es_propietario && !$es_admin) {
            $_SESSION['error_message'] = 'No tienes permiso para eliminar este equipo';
            $this->redirect('/equipos');
            return;
        }

        // Realizar soft delete
        $deleted = $this->equipoModel->softDelete($id);

        if ($deleted) {
            $_SESSION['success_message'] = 'Equipo eliminado exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el equipo';
        }

        $this->redirect('/equipos');
    }

    /**
     * Genera el código QR para un equipo
     * 
     * @param int $id ID del equipo
     * @return void
     */
    public function generateQR(int $id): void
    {
        Auth::requireAuth('/login');
        Middleware::requirePermission('equipos.actualizar');

        if (!$this->isPost()) {
            $this->redirect('/equipos/' . $id);
            return;
        }

        $user = Auth::user();
        $equipo = $this->equipoModel->getWithDetails($id);

        if (!$equipo) {
            $_SESSION['error_message'] = 'Equipo no encontrado';
            $this->redirect('/equipos');
            return;
        }

        // Verificar permisos
        $es_propietario = $equipo['id_usuario'] == $user['id_usuario'];
        $es_admin = in_array($user['nombre_rol'], ['Administrador', 'Administrativo']);

        if (!$es_propietario && !$es_admin) {
            $_SESSION['error_message'] = 'No tienes permiso para generar QR para este equipo';
            $this->redirect('/equipos');
            return;
        }

        try {
            // Generar el código QR
            $qrGenerator = new QRCodeGenerator();
            
            $qrData = $qrGenerator->generate([
                'id_equipo' => $equipo['id_equipo'],
                'id_usuario' => $equipo['id_usuario'],
                'numero_serie' => $equipo['numero_serie'],
                'nombre_usuario' => $equipo['nombres'] . ' ' . $equipo['apellidos']
            ], $id);

            // Guardar en la base de datos
            $this->qrModel->createQR($id, $qrData['codigo_qr'], $qrData['filepath']);

            $_SESSION['success_message'] = 'Código QR generado exitosamente';
        } catch (\Exception $e) {
            $_SESSION['error_message'] = 'Error al generar el código QR: ' . $e->getMessage();
        }

        $this->redirect('/equipos/' . $id);
    }

    /**
     * Sube las imágenes del equipo
     * 
     * @param int $equipo_id ID del equipo
     * @param array $files Array de archivos $_FILES
     * @return void
     */
    private function uploadImages(int $equipo_id, array $files): void
    {
        $upload_dir = EQUIPOS_UPLOAD_PATH;
        
        // Crear directorio si no existe
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $total_images = $this->imagenModel->countByEquipo($equipo_id);
        $uploaded = 0;

        foreach ($files['tmp_name'] as $key => $tmp_name) {
            // Validar que el archivo existe
            if (!is_uploaded_file($tmp_name)) {
                continue;
            }

            // Validar límite de imágenes
            if ($total_images + $uploaded >= 5) {
                break;
            }

            // Validar tipo
            $file_type = $files['type'][$key];
            if (!in_array($file_type, $allowed_types)) {
                continue;
            }

            // Validar tamaño
            if ($files['size'][$key] > $max_size) {
                continue;
            }

            // Generar nombre único
            $extension = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
            $filename = 'equipo_' . $equipo_id . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filepath = $upload_dir . '/' . $filename;
            $relative_path = '/uploads/equipos/' . $filename;

            // Mover archivo
            if (move_uploaded_file($tmp_name, $filepath)) {
                // Redimensionar imagen si es necesario
                $this->resizeImage($filepath, 1200, 1200);

                // Guardar en base de datos
                $tipo = ($total_images + $uploaded === 0) ? 'principal' : 'detalle';
                $this->imagenModel->saveImagen($equipo_id, $relative_path, $tipo);
                
                $uploaded++;
            }
        }
    }

    /**
     * Redimensiona una imagen manteniendo su proporción
     * 
     * @param string $filepath Ruta del archivo
     * @param int $max_width Ancho máximo
     * @param int $max_height Alto máximo
     * @return void
     */
    private function resizeImage(string $filepath, int $max_width, int $max_height): void
    {
        $image_info = getimagesize($filepath);
        if (!$image_info) {
            return;
        }

        list($width, $height, $type) = $image_info;

        // Si la imagen ya es más pequeña, no hacer nada
        if ($width <= $max_width && $height <= $max_height) {
            return;
        }

        // Calcular nuevas dimensiones
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = (int)($width * $ratio);
        $new_height = (int)($height * $ratio);

        // Crear imagen desde el archivo
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filepath);
                break;
            default:
                return;
        }

        // Crear nueva imagen redimensionada
        $destination = imagecreatetruecolor($new_width, $new_height);

        // Preservar transparencia para PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
        }

        // Redimensionar
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Guardar imagen
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filepath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filepath, 9);
                break;
        }

        // Liberar memoria
        imagedestroy($source);
        imagedestroy($destination);
    }
}

