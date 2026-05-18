# 🎓 Sistema de Gestión Escolar y Administrativa - CONALEP

## 📌 Descripción General
Sistema web integral desarrollado como proyecto de solución a medida para el *CONALEP*, diseñado para centralizar, automatizar y optimizar todos los procesos administrativos, financieros, académicos y de control escolar de la institución.

El proyecto nace con el objetivo principal de eliminar los procesos manuales, el papeleo físico y las validaciones presenciales que se realizaban anteriormente, reduciendo tiempos de operación, evitando errores humanos y permitiendo que la información esté disponible, segura y actualizada al instante para cada área correspondiente.

El sistema cuenta con una *gestión de roles y permisos totalmente diferenciada*, donde cada usuario accede únicamente a las funciones y vistas que le corresponden según su perfil, garantizando seguridad y confidencialidad en el manejo de los datos.

## ⚙️ Funcionalidades y Módulos por Rol

### 🔑 1. Administrador
Perfil con acceso total y control absoluto del sistema:
- Gestión completa de usuarios: Alta, baja, modificación y asignación de roles.
- Administración de catálogos generales: Alumnos, Docentes, Grupos, Talleres, Ciclos Escolares y Conceptos de pago.
- Acceso, edición y configuración de todos los módulos, registros y reportes del sistema.

### 📚 2. Tutores (Control de Grupos y Asistencia)
Módulo exclusivo para docentes encargados de grupo:
- Visualización restringida: Solo puede consultar y gestionar los grupos que le fueron asignados.
- *Automatización de reuniones:* Al crear un nuevo grupo, el sistema genera automáticamente *3 reuniones programadas por semestre* (correspondientes a los cortes de evaluación).
- Configuración de eventos: Posibilidad de modificar fecha, hora y *activar/desactivar* cada reunión. Solo cuando está activa se habilita el registro de asistencia.
- Control de asistencia: Registro de presencia de *padres de familia* en las juntas, asociado a cada alumno del grupo.

### 🔧 3. Talleres (Control de Adeudos)
Perfil diseñado para encargados de especialidades y laboratorios:
- Acceso limitado: Solo visualiza el grupo o taller específico que tiene asignado.
- Gestión de situación de alumnos: Función principal para *marcar o desmarcar adeudos* de material, herramientas o equipo a cada alumno.
- Registro de información que alimenta la base de datos para validaciones posteriores en el proceso de bajas.

### 💰 4. Finanzas (Control de Pagos y Cobranza)
Módulo financiero completo para la gestión de cartera y cobros institucionales:
- Catálogo de conceptos: Administración de todos los tipos de pago vigentes (Inscripción, Reinscripción, Cardex, Credencial, Exámenes, etc.).
- Generación de documentos: Creación automática de *fichas de pago en formato PDF*, listas para impresión o envío.
- Control de estatus financiero: Actualización y clasificación del estado de cuenta del alumno: Pendiente, Parcialmente pagado o Pagado.
- Historial financiero: Registro detallado de todos los movimientos y pagos realizados por el alumno a lo largo de su estancia, *exportable a PDF* desde su perfil.

### 📑 5. Servicios Escolares (Validaciones y Procesos de Baja)
Módulo estratégico que *resuelve el problema principal* de la institución, optimizando el flujo de trabajo:
- Vista general de información académica, financiera y de talleres, enfocada en la gestión de OAVOs y Talleres.
- *Automatización del proceso de baja:*
  > 🔄 Antes: El alumno debía recorrer físicamente cada área para obtener firmas de "no adeudo", proceso lento y propenso a errores.
  >
  > ⚡ Ahora: El personal busca al alumno por *nombre o matrícula. El sistema cruza automáticamente la información de talleres y finanzas. **Si el alumno no presenta adeudos*, el sistema habilita la opción para dar de baja el registro de forma inmediata, segura y validada.

---

## 🛠️ Tecnologías y Herramientas Utilizadas

- *Framework:* ⚡ *Laravel* - Desarrollo basado en arquitectura MVC, seguridad y código estructurado.
- *Lenguajes:* PHP 8+, HTML5, CSS3, JavaScript.
- *Gestión de Activos:* ⚡ *Vite* - Compilación y optimización de recursos estáticos.
- *Base de Datos:* 💾 *MySQL* - Diseño de base de datos relacional, normalizada y optimizada.
    - Características implementadas: Migraciones, Modelos, Relaciones complejas (1:1, 1:N, N:M), Llaves foráneas, Índices.
- *Generación de Documentos:* Librerías para creación y exportación automática de archivos *PDF*.
- *Autenticación y Seguridad:* Sistema de inicio de sesión seguro, encriptación de contraseñas y *Middleware de Roles* para restricción de accesos por perfil.
- *Entorno de Desarrollo:* XAMPP / Servidor Apache.
- *Panel de administracion:* Filament

## 🚀 Características Técnicas Destacadas

✅ *Control de Acceso:* Implementación de lógica de negocios para que cada usuario solo interactúe con lo que le corresponde.
✅ *Automatización de Lógica:* Generación automática de registros (reuniones) al momento de crear una entidad padre (grupo).
✅ *Consultas Optimizadas:* Cruce de información entre distintas tablas y módulos para validación de reglas de negocio.
✅ *Historial y Trazabilidad:* Registro completo de movimientos y situación del alumno en todas las áreas.
✅ *Interfaz Funcional:* Diseño enfocado en la facilidad de uso para personal administrativo sin conocimientos técnicos avanzados.

## 📖 Guía de Instalación y Ejecución

### 📋 Requisitos Previos
- Servidor web (Apache)
- PHP 8.1 o superior
- MySQL
- Composer

### ⚙️ Pasos de instalación
1.  Clonar o descargar el repositorio en el entorno local.
2.  Instalar dependencias del proyecto:
    bash
    composer install
    
3.  Copiar el archivo de ejemplo de entorno y configurar según el entorno local:
    bash
    cp .env.example .env
    
4.  Generar la clave de aplicación:
    bash
    php artisan key:generate
    
5.  Ejecutar las migraciones para crear la estructura de la base de datos:
    bash
    php artisan migrate
    
6.  Levantar el servidor de desarrollo:
    bash
    php artisan serve
    

### ⚠️ Aviso Legal y de Uso
Este sistema fue desarrollado como parte de un proyecto académico y profesional para la institución mencionada. 
El código y archivos aquí presentados *corresponden a la estructura, lógica y diseño del sistema*. 
Se ha realizado una adaptación para este repositorio, por lo que *no contiene datos reales, credenciales de acceso, rutas de producción ni información confidencial* de la institución ni de sus usuarios. 
Este repositorio tiene como único fin *demostrar las habilidades de desarrollo, conocimientos técnicos y experiencia del autor*, 
y no constituye una liberación de software oficial, ni una copia exacta del sistema en producción, ni autoriza su uso comercial o distribución.