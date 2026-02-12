-- ============================================================================
-- SISTEMA DE GESTIÓN MULTI-SINDICATO
-- Base de datos completa con módulos de préstamos, caja, transparencia y más
-- ============================================================================

-- ============================================================================
-- MÓDULO: SINDICATOS Y CONFIGURACIÓN
-- ============================================================================

CREATE TABLE sindicatos
(
    sindicato_id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre              VARCHAR(255) NOT NULL UNIQUE,
    abreviacion         VARCHAR(50)  NOT NULL UNIQUE,

    -- Datos de Contacto
    direccion           VARCHAR(255),
    telefono            VARCHAR(20),
    correo              VARCHAR(100),
    facebook            VARCHAR(255),
    sitio_web           VARCHAR(255),

    -- Branding
    logo                VARCHAR(255),
    eslogan             VARCHAR(255),

    -- Identidad Institucional
    mision              TEXT,
    vision              TEXT,
    objetivo            TEXT,
    compromiso          TEXT,

    -- Datos Legales/Fiscales
    rfc                 VARCHAR(13),
    representante_legal VARCHAR(255),

    -- Control
    activo              BOOLEAN  DEFAULT TRUE,
    fecha_creacion      DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_activo (activo),
    INDEX idx_nombre (nombre)
);

-- Valores institucionales del sindicato
CREATE TABLE sindicato_valores
(
    valor_id     INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id INT  NOT NULL,
    valor        TEXT NOT NULL,
    orden        INT  NOT NULL DEFAULT 1,

    CONSTRAINT fk_valores_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_orden (sindicato_id, orden)
);

-- Puestos personalizables del comité sindical
CREATE TABLE sindicato_puestos
(
    puesto_id        INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id     INT          NOT NULL,
    nombre_puesto    VARCHAR(100) NOT NULL,
    descripcion      TEXT,
    orden_jerarquico INT DEFAULT 0, -- Para ordenar en organigrama

    CONSTRAINT fk_puesto_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_puesto (sindicato_id, orden_jerarquico)
);

-- Integrantes del comité ejecutivo
CREATE TABLE sindicato_integrante_comite
(
    integrante_id  INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id   INT          NOT NULL,
    puesto_id      INT          NOT NULL,
    nombre         VARCHAR(255) NOT NULL,
    periodo_inicio DATE,
    periodo_fin    DATE,
    foto           VARCHAR(255),
    biografia      TEXT,
    activo         BOOLEAN DEFAULT TRUE,

    CONSTRAINT fk_integrante_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_integrante_puesto
        FOREIGN KEY (puesto_id)
            REFERENCES sindicato_puestos (puesto_id)
            ON DELETE CASCADE,

    INDEX idx_periodo_activo (sindicato_id, activo, periodo_fin)
);

-- Configuraciones generales del sindicato (clave-valor flexible)
CREATE TABLE sindicato_configuraciones
(
    configuracion_id INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id     INT          NOT NULL,
    clave            VARCHAR(100) NOT NULL,
    valor            TEXT         NOT NULL,
    tipo             ENUM ('texto', 'color', 'numero', 'json', 'bool') DEFAULT 'texto',
    descripcion      VARCHAR(255),

    UNIQUE KEY uk_sindicato_clave (sindicato_id, clave),
    CONSTRAINT fk_configuraciones_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_clave (sindicato_id, clave)
);

-- Catálogo de tasas de interés según perfil del usuario
CREATE TABLE cat_tasas_interes
(
    tasa_id               INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id          INT           NOT NULL,
    nombre                VARCHAR(100)  NOT NULL, -- Ej: "Agremiado Ahorrador"
    es_agremiado          BOOLEAN       NOT NULL,
    es_ahorrador          BOOLEAN       NOT NULL,
    tasa_anual            DECIMAL(5, 2) NOT NULL, -- Ej: 6.00, 7.50, 8.00, 9.50
    activa                BOOLEAN DEFAULT TRUE,
    fecha_vigencia_inicio DATE,
    fecha_vigencia_fin    DATE,

    CONSTRAINT fk_tasa_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_tasa_perfil (sindicato_id, es_agremiado, es_ahorrador, activa)
);

-- Catálogo de tipos de ingresos/prestaciones
-- Ejemplos: Quincena, Aguinaldo, Bono Día del Padre, Fondo de Ahorro
CREATE TABLE cat_tipos_ingreso
(
    tipo_ingreso_id    INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id       INT          NOT NULL,
    nombre             VARCHAR(100) NOT NULL, -- "Aguinaldo", "Quincena", "Bono"
    descripcion        TEXT,
    es_periodico       BOOLEAN DEFAULT FALSE, -- TRUE para Quincena, FALSE para bonos anuales
    frecuencia_dias    INT,                   -- 15 para quincenas, NULL para anuales
    mes_pago_tentativo INT,                   -- Para prestaciones: 12 para Diciembre
    dia_pago_tentativo INT,                   -- 15 o 20 típicamente
    activo             BOOLEAN DEFAULT TRUE,

    CONSTRAINT fk_cat_ingreso_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_tipo_ingreso_activo (sindicato_id, activo)
);

-- ============================================================================
-- MÓDULO: ROLES Y PERMISOS (RBAC)
-- ============================================================================

-- Catálogo de roles disponibles en el sistema
CREATE TABLE cat_roles
(
    rol_id         INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id   INT,                   -- NULL para roles globales (super_admin)
    nombre         VARCHAR(100) NOT NULL,
    descripcion    TEXT,
    es_rol_sistema BOOLEAN DEFAULT FALSE, -- TRUE para roles predefinidos
    activo         BOOLEAN DEFAULT TRUE,

    UNIQUE KEY uk_sindicato_nombre_rol (sindicato_id, nombre),
    CONSTRAINT fk_rol_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_rol_activo (sindicato_id, activo)
);

-- Catálogo de permisos granulares del sistema
CREATE TABLE cat_permisos
(
    permiso_id  INT AUTO_INCREMENT PRIMARY KEY,
    modulo      VARCHAR(100) NOT NULL, -- 'prestamos', 'caja', 'transparencia', 'usuarios'
    accion      VARCHAR(100) NOT NULL, -- 'crear', 'leer', 'actualizar', 'eliminar', 'aprobar'
    nombre      VARCHAR(255) NOT NULL,
    descripcion TEXT,

    UNIQUE KEY uk_modulo_accion (modulo, accion),
    INDEX idx_modulo (modulo)
);

-- Relación roles-permisos (qué puede hacer cada rol)
CREATE TABLE rol_permisos
(
    rol_permiso_id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id         INT NOT NULL,
    permiso_id     INT NOT NULL,

    UNIQUE KEY uk_rol_permiso (rol_id, permiso_id),
    CONSTRAINT fk_rol_permiso_rol
        FOREIGN KEY (rol_id)
            REFERENCES cat_roles (rol_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_rol_permiso_permiso
        FOREIGN KEY (permiso_id)
            REFERENCES cat_permisos (permiso_id)
            ON DELETE CASCADE,

    INDEX idx_rol (rol_id),
    INDEX idx_permiso (permiso_id)
);

-- ============================================================================
-- MÓDULO: USUARIOS
-- ============================================================================

CREATE TABLE usuarios
(
    usuario_id            VARCHAR(36) PRIMARY KEY, -- UUID
    sindicato_id          INT          NOT NULL,

    -- Autenticación
    correo                VARCHAR(255) NOT NULL UNIQUE,
    contra                VARCHAR(255) NOT NULL,
    activo                BOOLEAN  DEFAULT TRUE,

    -- Clasificación (define tasa de interés aplicable)
    es_agremiado          BOOLEAN  DEFAULT TRUE,
    es_ahorrador          BOOLEAN  DEFAULT FALSE,

    -- Datos Personales
    nombre                VARCHAR(100) NOT NULL,
    apellidos             VARCHAR(255) NOT NULL,
    curp                  VARCHAR(20) UNIQUE,
    rfc                   VARCHAR(13),
    nss                   VARCHAR(15),
    fecha_nacimiento      DATE,
    foto                  VARCHAR(255),

    -- Contacto
    telefono              VARCHAR(20),
    direccion             VARCHAR(255),

    -- Datos Bancarios (para depósito de préstamos)
    banco_nombre          VARCHAR(100),
    cuenta_bancaria       VARCHAR(20),
    clabe_interbancaria   VARCHAR(18),

    -- Datos Laborales
    categoria             VARCHAR(100),
    departamento          VARCHAR(100),
    salario_base          DECIMAL(10, 2),
    salario_quincenal     DECIMAL(10, 2),
    fecha_ingreso_laboral DATE,

    -- Control de Sesión
    ultimo_ingreso        DATETIME,
    fecha_creacion        DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_eliminacion     DATETIME default NULL,

    CONSTRAINT fk_usuario_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_correo (correo),
    INDEX idx_nombre_apellidos (nombre, apellidos),
    INDEX idx_perfil_interes (sindicato_id, es_agremiado, es_ahorrador),
    INDEX idx_activo (activo)
);

-- Asignación de roles a usuarios (un usuario puede tener múltiples roles)
CREATE TABLE usuario_roles
(
    usuario_rol_id   INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id       VARCHAR(36) NOT NULL,
    rol_id           INT         NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    asignado_por     VARCHAR(36), -- usuario_id de quien asignó el rol

    UNIQUE KEY uk_usuario_rol (usuario_id, rol_id),
    CONSTRAINT fk_usr_rol_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_usr_rol_rol
        FOREIGN KEY (rol_id)
            REFERENCES cat_roles (rol_id)
            ON DELETE CASCADE,

    INDEX idx_usuario (usuario_id),
    INDEX idx_rol (rol_id)
);

-- Documentación requerida del usuario
CREATE TABLE usuario_documentacion
(
    documento_id     INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id       VARCHAR(36)  NOT NULL,
    tipo_documento   VARCHAR(100) NOT NULL, -- 'afiliacion', 'ine', 'comprobante_domicilio', etc.
    ruta_archivo     VARCHAR(255) NOT NULL,
    estado           ENUM ('pendiente', 'validado', 'rechazado') DEFAULT 'pendiente',
    observaciones    TEXT,
    fecha_subida     DATETIME                                    DEFAULT CURRENT_TIMESTAMP,
    fecha_validacion DATETIME,
    validado_por     VARCHAR(36),           -- usuario_id de quien validó

    CONSTRAINT fk_documentos_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE CASCADE,

    INDEX idx_usuario_tipo (usuario_id, tipo_documento),
    INDEX idx_estado (estado)
);

-- ============================================================================
-- MÓDULO: PRÉSTAMOS
-- ============================================================================

-- Cabecera del préstamo (workflow completo)
CREATE TABLE prestamos
(
    prestamo_id                  INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id                   VARCHAR(36)    NOT NULL,
    sindicato_id                 INT            NOT NULL,

    -- Identificación
    folio                        VARCHAR(50) UNIQUE,      -- Generado automáticamente: SIN-2025-001

    -- Montos
    monto_solicitado             DECIMAL(10, 2) NOT NULL,
    monto_aprobado               DECIMAL(10, 2),
    tasa_interes_aplicada        DECIMAL(5, 2)  NOT NULL, -- % exacto usado (puede ser personalizado)
    tasa_moratorio_diario        DECIMAL(5, 4),           -- Para calcular picos por retraso
    total_a_pagar_estimado       DECIMAL(10, 2),
    saldo_pendiente              DECIMAL(10, 2),          -- Se actualiza con cada pago

    -- Plazos
    plazo_meses                  INT,
    plazo_quincenas              INT,
    fecha_primer_pago            DATE,
    fecha_ultimo_pago_programado DATE,

    -- Fechas del Workflow
    fecha_solicitud              DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_revision_documental    DATETIME,
    fecha_aprobacion             DATETIME,
    fecha_generacion_documentos  DATETIME,                -- Cuando se creó pagaré
    fecha_validacion_firmas      DATETIME,
    fecha_desembolso             DATETIME,                -- Inicio de devengo de intereses
    fecha_liquidacion_total      DATETIME,

    -- Estado del Flujo
    estado                       ENUM (
        'borrador',                                       -- Usuario llenando solicitud
        'revision_documental',                            -- Admin revisando estados de cuenta
        'correccion_requerida',                           -- Docs rechazados, usuario corrige
        'aprobado_pendiente_firma',-- Pagarés generados, esperando firma
        'validacion_firmas',                              -- Firmas subidas, finanzas valida
        'activo',                                         -- Dinero entregado, corriendo
        'pagado',                                         -- Deuda saldada
        'vencido',                                        -- Tiene pagos atrasados
        'reestructurado',                                 -- Se generó nuevo préstamo para cubrir
        'cancelado'                                       -- Cancelado antes de desembolso
        )                                 DEFAULT 'borrador',

    -- Referencias
    prestamo_origen_id           INT,                     -- Si es reestructuración, apunta al original
    motivo_rechazo               TEXT,
    observaciones_admin          TEXT,
    observaciones_internas       TEXT,                    -- Notas privadas del comité

    -- Firmas digitales de documentos generados
    firmante_finanzas            VARCHAR(255),            -- Nombre del secretario de finanzas
    firmante_prestamista         VARCHAR(255),            -- Confirmación del usuario

    -- Control
    requiere_reestructuracion    BOOLEAN  DEFAULT FALSE,
    creado_por                   VARCHAR(36),             -- Admin que procesó
    fecha_eliminacion            DATETIME default NULL,

    CONSTRAINT fk_prestamo_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_prestamo_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_prestamo_origen
        FOREIGN KEY (prestamo_origen_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE SET NULL,

    INDEX idx_folio (folio),
    INDEX idx_usuario_estado (usuario_id, estado),
    INDEX idx_estado_fecha (estado, fecha_solicitud),
    INDEX idx_origen (prestamo_origen_id)
);

-- Configuración de pagos del préstamo (mix nómina + prestaciones)
CREATE TABLE prestamo_configuracion_pagos
(
    config_pago_id             INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id                INT            NOT NULL,
    tipo_ingreso_id            INT            NOT NULL,

    -- Configuración
    monto_total_a_descontar    DECIMAL(10, 2) NOT NULL,                               -- Total de esta fuente
    numero_cuotas              INT                                         DEFAULT 1, -- Quincenas: 24, Aguinaldo: 1
    monto_por_cuota            DECIMAL(10, 2),                                        -- Para quincenas

    -- Método de cálculo de interés
    metodo_interes             ENUM ('simple_aleman', 'compuesto')         DEFAULT 'simple_aleman',
    -- Simple alemán: para quincenas (cuota fija de capital + interés variable)
    -- Compuesto: para prestaciones (un solo pago)

    -- Documento probatorio
    ruta_documento_comprobante VARCHAR(255),                                          -- Estado de cuenta de esa prestación
    estado_documento           ENUM ('pendiente', 'validado', 'rechazado') DEFAULT 'pendiente',
    observaciones_documento    TEXT,
    fecha_validacion_documento DATETIME,

    CONSTRAINT fk_config_prestamo
        FOREIGN KEY (prestamo_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_config_tipo_ingreso
        FOREIGN KEY (tipo_ingreso_id)
            REFERENCES cat_tipos_ingreso (tipo_ingreso_id)
            ON DELETE RESTRICT,

    INDEX idx_prestamo (prestamo_id),
    INDEX idx_tipo_ingreso (tipo_ingreso_id)
);

-- Documentos legales generados del préstamo
CREATE TABLE prestamo_documentos_legales
(
    doc_legal_id                 INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id                  INT          NOT NULL,
    tipo_documento               ENUM (
        'pagare',
        'anuencia_descuento',
        'corrida_financiera',
        'comprobante_transferencia',
        'contrato_prestamo',
        'carta_reestructuracion'
        )                                     NOT NULL,

    ruta_archivo                 VARCHAR(255) NOT NULL,
    version                      INT      DEFAULT 1, -- Si se regenera por reestructuración

    -- Control de firmas
    requiere_firma_usuario       BOOLEAN  DEFAULT FALSE,
    firma_usuario_url            VARCHAR(255),       -- Archivo firmado subido
    fecha_firma_usuario          DATETIME,

    requiere_validacion_finanzas BOOLEAN  DEFAULT FALSE,
    validado_por_finanzas        BOOLEAN  DEFAULT FALSE,
    validado_por                 VARCHAR(36),        -- usuario_id
    fecha_validacion             DATETIME,
    observaciones_validacion     TEXT,

    fecha_generacion             DATETIME DEFAULT CURRENT_TIMESTAMP,
    generado_por                 VARCHAR(36),        -- usuario_id

    CONSTRAINT fk_doc_legal_prestamo
        FOREIGN KEY (prestamo_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE CASCADE,

    INDEX idx_prestamo_tipo (prestamo_id, tipo_documento),
    INDEX idx_pendientes_firma (requiere_firma_usuario, fecha_firma_usuario)
);

-- Tabla de amortización (corrida financiera)
CREATE TABLE prestamo_amortizacion
(
    amortizacion_id            INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id                INT            NOT NULL,

    -- Identificación del pago
    numero_pago                INT            NOT NULL,                                                -- 1, 2, 3... N
    tipo_ingreso_id            INT            NOT NULL,                                                -- De qué fuente sale este pago
    fecha_programada           DATE           NOT NULL,                                                -- 15 o 20 del mes

    -- Desglose Financiero Programado (calculado al generar tabla)
    saldo_inicial              DECIMAL(10, 2) NOT NULL,
    capital                    DECIMAL(10, 2) NOT NULL,
    interes_ordinario          DECIMAL(10, 2) NOT NULL,
    pago_total_programado      DECIMAL(10, 2) NOT NULL,                                                -- capital + interes
    saldo_final                DECIMAL(10, 2) NOT NULL,

    -- Control de Pagos Reales
    estado_pago                ENUM ('pendiente', 'pagado', 'pagado_parcial', 'vencido') DEFAULT 'pendiente',
    fecha_pago_real            DATETIME,
    monto_pagado_real          DECIMAL(10, 2)                                            DEFAULT 0,

    -- Intereses Moratorios (picos por atraso)
    dias_atraso                INT                                                       DEFAULT 0,
    interes_moratorio_generado DECIMAL(10, 2)                                            DEFAULT 0,

    -- Trazabilidad
    pagado_por                 VARCHAR(36),                                                            -- usuario_id que registró el pago
    comprobante_pago           VARCHAR(255),                                                           -- URL del comprobante

    -- Control de regeneración
    version_tabla              INT                                                       DEFAULT 1,    -- Incrementa con reestructuraciones
    activa                     BOOLEAN                                                   DEFAULT TRUE, -- FALSE si se regeneró la tabla

    CONSTRAINT fk_amort_prestamo
        FOREIGN KEY (prestamo_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_amort_tipo_ingreso
        FOREIGN KEY (tipo_ingreso_id)
            REFERENCES cat_tipos_ingreso (tipo_ingreso_id)
            ON DELETE RESTRICT,

    INDEX idx_prestamo_numero (prestamo_id, numero_pago),
    INDEX idx_fecha_estado (fecha_programada, estado_pago),
    INDEX idx_version_activa (prestamo_id, version_tabla, activa)
);

-- Pagos extraordinarios (anticipos, abonos adicionales)
CREATE TABLE prestamo_pagos_extraordinarios
(
    pago_extraordinario_id      INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id                 INT                                                     NOT NULL,

    tipo_pago                   ENUM ('anticipo', 'liquidacion_total', 'abono_capital') NOT NULL,
    monto                       DECIMAL(10, 2)                                          NOT NULL,
    fecha_pago                  DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Aplicación del pago
    aplicado_a_capital          DECIMAL(10, 2),
    aplicado_a_interes          DECIMAL(10, 2),
    aplicado_a_moratorio        DECIMAL(10, 2),

    -- Efecto
    regenero_tabla_amortizacion BOOLEAN  DEFAULT TRUE,
    version_tabla_generada      INT,         -- Nueva versión de amortización creada

    observaciones               TEXT,
    comprobante_pago            VARCHAR(255),
    registrado_por              VARCHAR(36), -- usuario_id

    CONSTRAINT fk_pago_extra_prestamo
        FOREIGN KEY (prestamo_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE CASCADE,

    INDEX idx_prestamo_fecha (prestamo_id, fecha_pago),
    INDEX idx_tipo (tipo_pago)
);

-- Historial de reestructuraciones
CREATE TABLE prestamo_reestructuraciones
(
    reestructuracion_id      INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_original_id     INT            NOT NULL,
    prestamo_nuevo_id        INT            NOT NULL,

    motivo                   ENUM (
        'pago_anticipado',
        'picos_acumulados',
        'solicitud_cliente',
        'ajuste_administrativo'
        )                                   NOT NULL,

    saldo_pendiente_original DECIMAL(10, 2) NOT NULL,
    intereses_pendientes     DECIMAL(10, 2) NOT NULL,
    moratorios_pendientes    DECIMAL(10, 2) NOT NULL,
    nuevo_monto_total        DECIMAL(10, 2) NOT NULL,
    nueva_tasa_interes       DECIMAL(5, 2),
    nuevo_plazo_quincenas    INT,

    fecha_reestructuracion   DATETIME DEFAULT CURRENT_TIMESTAMP,
    autorizado_por           VARCHAR(36), -- usuario_id
    observaciones            TEXT,

    CONSTRAINT fk_reest_original
        FOREIGN KEY (prestamo_original_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_reest_nuevo
        FOREIGN KEY (prestamo_nuevo_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE RESTRICT,

    INDEX idx_original (prestamo_original_id),
    INDEX idx_nuevo (prestamo_nuevo_id),
    INDEX idx_fecha (fecha_reestructuracion)
);

-- Comprobantes generados automáticamente
CREATE TABLE prestamo_comprobantes
(
    comprobante_id    INT AUTO_INCREMENT PRIMARY KEY,
    prestamo_id       INT                NOT NULL,
    amortizacion_id   INT,          -- NULL si es comprobante de desembolso

    tipo_comprobante  ENUM (
        'desembolso',
        'pago_regular',
        'pago_extraordinario',
        'cargo_moratorio',
        'ajuste'
        )                                NOT NULL,

    folio_comprobante VARCHAR(50) UNIQUE NOT NULL,
    monto             DECIMAL(10, 2)     NOT NULL,
    descripcion       TEXT,

    fecha_emision     DATETIME DEFAULT CURRENT_TIMESTAMP,
    ruta_pdf          VARCHAR(255), -- PDF generado automáticamente

    CONSTRAINT fk_comp_prestamo
        FOREIGN KEY (prestamo_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_comp_amortizacion
        FOREIGN KEY (amortizacion_id)
            REFERENCES prestamo_amortizacion (amortizacion_id)
            ON DELETE SET NULL,

    INDEX idx_folio (folio_comprobante),
    INDEX idx_prestamo_fecha (prestamo_id, fecha_emision)
);

-- ============================================================================
-- MÓDULO: CAJA Y CONTROL FINANCIERO
-- ============================================================================

-- Cajas del sindicato (puede tener múltiples: general, eventos, etc.)
CREATE TABLE cajas
(
    caja_id        INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id   INT            NOT NULL,
    nombre         VARCHAR(255)   NOT NULL,
    descripcion    TEXT,
    saldo_actual   DECIMAL(15, 2) NOT NULL DEFAULT 0,
    activa         BOOLEAN                 DEFAULT TRUE,
    version        INT                     DEFAULT 1,
    fecha_creacion DATETIME                DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_caja_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_activa (sindicato_id, activa)
);

-- Categorías para clasificar movimientos (transparencia)
CREATE TABLE cat_categorias_transaccion
(
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id INT                        NOT NULL,
    nombre       VARCHAR(100)               NOT NULL, -- "Préstamos", "Gastos Operativos", "Eventos"
    tipo         ENUM ('ingreso', 'egreso') NOT NULL,
    descripcion  TEXT,
    activa       BOOLEAN DEFAULT TRUE,

    CONSTRAINT fk_categoria_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_tipo (sindicato_id, tipo, activa)
);

-- Movimientos de caja (con trazabilidad completa)
CREATE TABLE caja_movimientos
(
    movimiento_id          INT AUTO_INCREMENT PRIMARY KEY,
    caja_id                INT                        NOT NULL,
    categoria_id           INT                        NOT NULL,

    tipo                   ENUM ('ingreso', 'egreso') NOT NULL,
    monto                  DECIMAL(10, 2)             NOT NULL,
    descripcion            TEXT                       NOT NULL,

    -- Referencias para trazabilidad
    usuario_relacionado_id VARCHAR(36),                         -- Quién pagó o recibió
    prestamo_id            INT,                                 -- Si es desembolso o abono de préstamo

    -- Documentación obligatoria
    comprobante_url        VARCHAR(255)               NOT NULL, -- Factura, recibo, transferencia
    requiere_aprobacion    BOOLEAN  DEFAULT FALSE,
    aprobado               BOOLEAN  DEFAULT FALSE,
    aprobado_por           VARCHAR(36),                         -- usuario_id
    fecha_aprobacion       DATETIME,

    -- Control de cortes
    corte_id               INT,                                 -- Al cerrar quincena se asigna
    conciliado             BOOLEAN  DEFAULT FALSE,

    -- Auditoría
    fecha_movimiento       DATETIME DEFAULT CURRENT_TIMESTAMP,
    creado_por             VARCHAR(36)                NOT NULL, -- usuario_id que registró
    fecha_creacion         DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_eliminacion      DATETIME default NULL,

    CONSTRAINT fk_mov_caja
        FOREIGN KEY (caja_id)
            REFERENCES cajas (caja_id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_mov_categoria
        FOREIGN KEY (categoria_id)
            REFERENCES cat_categorias_transaccion (categoria_id)
            ON DELETE RESTRICT,
    CONSTRAINT fk_mov_usuario_rel
        FOREIGN KEY (usuario_relacionado_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE SET NULL,
    CONSTRAINT fk_mov_prestamo
        FOREIGN KEY (prestamo_id)
            REFERENCES prestamos (prestamo_id)
            ON DELETE SET NULL,

    INDEX idx_caja_fecha (caja_id, fecha_movimiento),
    INDEX idx_tipo_fecha (tipo, fecha_movimiento),
    INDEX idx_corte (corte_id),
    INDEX idx_prestamo (prestamo_id)
);

-- Cortes de caja quincenales
CREATE TABLE caja_cortes
(
    corte_id         INT AUTO_INCREMENT PRIMARY KEY,
    caja_id          INT            NOT NULL,

    periodo_inicio   DATE           NOT NULL,
    periodo_fin      DATE           NOT NULL,

    saldo_inicial    DECIMAL(10, 2) NOT NULL,
    total_ingresos   DECIMAL(15, 2) NOT NULL,
    total_egresos    DECIMAL(15, 2) NOT NULL,
    saldo_final      DECIMAL(15, 2) NOT NULL,

    -- Control
    estado           ENUM ('abierto', 'cerrado', 'conciliado') DEFAULT 'abierto',
    fecha_cierre     DATETIME,
    cerrado_por      VARCHAR(36),  -- usuario_id

    observaciones    TEXT,
    ruta_reporte_pdf VARCHAR(255), -- Reporte generado

    CONSTRAINT fk_corte_caja
        FOREIGN KEY (caja_id)
            REFERENCES cajas (caja_id)
            ON DELETE RESTRICT,

    INDEX idx_caja_periodo (caja_id, periodo_inicio, periodo_fin),
    INDEX idx_estado (estado)
);

-- ============================================================================
-- MÓDULO: TRANSPARENCIA Y GESTIÓN DOCUMENTAL
-- ============================================================================

-- Categorías de documentos
CREATE TABLE transparencia_categorias
(
    categoria_doc_id INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id     INT          NOT NULL,
    nombre           VARCHAR(100) NOT NULL, -- "Finanzas", "Actas", "Gestiones", "Legal"
    descripcion      TEXT,
    icono            VARCHAR(50),           -- Para UI
    orden            INT DEFAULT 0,

    CONSTRAINT fk_transp_cat_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_orden (sindicato_id, orden)
);

-- Archivos de transparencia
CREATE TABLE transparencia_archivos
(
    archivo_id          INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id        INT          NOT NULL,
    categoria_doc_id    INT          NOT NULL,

    nombre_archivo      VARCHAR(255) NOT NULL,
    descripcion         TEXT,
    ruta_almacenamiento VARCHAR(255) NOT NULL,
    tipo_archivo        VARCHAR(50),           -- 'pdf', 'xlsx', 'docx'
    tamano_bytes        BIGINT,

    -- Organización temporal
    anio                INT          NOT NULL,
    mes                 INT          NOT NULL,

    -- Control de acceso
    es_publico          BOOLEAN  DEFAULT TRUE, -- TRUE: todos ven. FALSE: solo agremiados/admin

    -- Metadatos
    etiquetas           JSON,                  -- ["presupuesto", "2025", "aprobado"]

    -- Control
    activo              BOOLEAN  DEFAULT TRUE,
    fecha_subida        DATETIME DEFAULT CURRENT_TIMESTAMP,
    subido_por          VARCHAR(36)  NOT NULL,
    numero_descargas    INT      DEFAULT 0,

    CONSTRAINT fk_archivo_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_archivo_categoria
        FOREIGN KEY (categoria_doc_id)
            REFERENCES transparencia_categorias (categoria_doc_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_anio_mes (sindicato_id, anio, mes),
    INDEX idx_publico (es_publico, activo),
    INDEX idx_busqueda_nombre (nombre_archivo),
    FULLTEXT INDEX idx_fulltext_descripcion (nombre_archivo, descripcion)
);

-- ============================================================================
-- MÓDULO: PUBLICACIONES Y COMUNICACIÓN
-- ============================================================================

-- Publicaciones (noticias, eventos, avisos)
CREATE TABLE publicaciones
(
    publicacion_id    INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id      INT          NOT NULL,

    titulo            VARCHAR(255) NOT NULL,
    resumen           VARCHAR(500),
    contenido         LONGTEXT     NOT NULL,

    tipo              ENUM ('noticia', 'evento', 'gestion', 'aviso') DEFAULT 'noticia',

    -- Prioridad y vigencia
    importante        BOOLEAN                                        DEFAULT FALSE, -- Destacar en portada
    fijado            BOOLEAN                                        DEFAULT FALSE, -- Siempre al inicio
    fecha_expiracion  DATE,                                                         -- NULL = no expira

    -- Control
    activo            BOOLEAN                                        DEFAULT TRUE,
    publicado         BOOLEAN                                        DEFAULT FALSE,
    fecha_publicacion DATETIME,
    fecha_creacion    DATETIME                                       DEFAULT CURRENT_TIMESTAMP,
    creado_por        VARCHAR(36)  NOT NULL,

    -- Engagement
    numero_vistas     INT                                            DEFAULT 0,

    CONSTRAINT fk_publicacion_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,

    INDEX idx_sindicato_publicado (sindicato_id, publicado, activo),
    INDEX idx_fecha_pub (fecha_publicacion),
    INDEX idx_importante_fijado (importante, fijado)
);

-- Imágenes de publicaciones
CREATE TABLE publicacion_imagenes
(
    imagen_id      INT AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT          NOT NULL,
    ruta           VARCHAR(255) NOT NULL,
    orden          INT     DEFAULT 0,
    es_portada     BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_imagen_publicacion
        FOREIGN KEY (publicacion_id)
            REFERENCES publicaciones (publicacion_id)
            ON DELETE CASCADE,

    INDEX idx_publicacion_orden (publicacion_id, orden)
);

-- Archivos adjuntos en publicaciones
CREATE TABLE publicacion_adjuntos
(
    adjunto_id     INT AUTO_INCREMENT PRIMARY KEY,
    publicacion_id INT          NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta           VARCHAR(255) NOT NULL,
    tipo_archivo   VARCHAR(50),
    tamano_bytes   BIGINT,

    CONSTRAINT fk_adjunto_publicacion
        FOREIGN KEY (publicacion_id)
            REFERENCES publicaciones (publicacion_id)
            ON DELETE CASCADE,

    INDEX idx_publicacion (publicacion_id)
);

-- ============================================================================
-- MÓDULO: MENSAJERÍA Y COMUNICACIÓN
-- ============================================================================

-- Hilo principal de conversación
CREATE TABLE hilos_contacto
(
    hilo_id              INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id         INT          NOT NULL,
    usuario_id           VARCHAR(36), -- NULL si es visitante externo

    tipo                 ENUM (
        'contacto_general',
        'transparencia_duda',
        'buzon_quejas',
        'soporte_prestamo',
        'solicitud_informacion'
        )                             NOT NULL,

    asunto               VARCHAR(255) NOT NULL,
    prioridad            ENUM ('baja', 'media', 'alta', 'urgente')               DEFAULT 'media',
    estado               ENUM ('abierto', 'en_proceso', 'respondido', 'cerrado') DEFAULT 'abierto',

    -- Datos de contacto externo (si no está logueado)
    nombre_externo       VARCHAR(100),
    correo_externo       VARCHAR(100),
    telefono_externo     VARCHAR(20),

    -- Asignación
    asignado_a           VARCHAR(36), -- usuario_id del admin/staff

    -- Control
    fecha_creacion       DATETIME                                                DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_mensaje DATETIME,
    fecha_cierre         DATETIME,

    CONSTRAINT fk_hilo_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_hilo_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE SET NULL,

    INDEX idx_sindicato_estado (sindicato_id, estado),
    INDEX idx_tipo_estado (tipo, estado),
    INDEX idx_asignado (asignado_a)
);

-- Mensajes dentro del hilo
CREATE TABLE mensajes_detalle
(
    mensaje_id         INT AUTO_INCREMENT PRIMARY KEY,
    hilo_id            INT  NOT NULL,

    -- Autor (puede ser usuario logueado o externo)
    autor_usuario_id   VARCHAR(36), -- NULL si es externo
    es_respuesta_staff BOOLEAN  DEFAULT FALSE,

    mensaje            TEXT NOT NULL,
    adjunto_url        VARCHAR(255),

    -- Control
    fecha_envio        DATETIME DEFAULT CURRENT_TIMESTAMP,
    leido              BOOLEAN  DEFAULT FALSE,
    fecha_lectura      DATETIME,

    CONSTRAINT fk_mensaje_hilo
        FOREIGN KEY (hilo_id)
            REFERENCES hilos_contacto (hilo_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_mensaje_autor
        FOREIGN KEY (autor_usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE SET NULL,

    INDEX idx_hilo_fecha (hilo_id, fecha_envio),
    INDEX idx_no_leidos (leido, es_respuesta_staff)
);

-- ============================================================================
-- MÓDULO: CREDENCIALES Y EXTRAS
-- ============================================================================

-- Credenciales generadas para agremiados
CREATE TABLE credenciales
(
    credencial_id       INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id          VARCHAR(36)        NOT NULL,

    numero_credencial   VARCHAR(50) UNIQUE NOT NULL,
    fecha_emision       DATE               NOT NULL,
    fecha_vencimiento   DATE,

    activa              BOOLEAN DEFAULT TRUE,
    ruta_pdf            VARCHAR(255), -- PDF de la credencial
    ruta_imagen_frontal VARCHAR(255),
    ruta_imagen_trasera VARCHAR(255),

    CONSTRAINT fk_credencial_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE CASCADE,

    INDEX idx_numero (numero_credencial),
    INDEX idx_usuario_activa (usuario_id, activa)
);

-- Registro de felicitaciones de cumpleaños
CREATE TABLE felicitaciones_cumpleanos
(
    felicitacion_id    INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id         VARCHAR(36) NOT NULL,
    anio               INT         NOT NULL,
    fecha_felicitacion DATE        NOT NULL,
    enviado            BOOLEAN                           DEFAULT FALSE,
    medio              ENUM ('correo', 'sistema', 'sms') DEFAULT 'sistema',

    CONSTRAINT fk_felicitacion_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE CASCADE,

    UNIQUE KEY uk_usuario_anio (usuario_id, anio),
    INDEX idx_fecha_enviado (fecha_felicitacion, enviado)
);

-- Agendamiento de citas
CREATE TABLE citas
(
    cita_id          INT AUTO_INCREMENT PRIMARY KEY,
    sindicato_id     INT          NOT NULL,
    usuario_id       VARCHAR(36),           -- Puede ser NULL si es externo

    tipo_cita        VARCHAR(100) NOT NULL, -- "Asesoría", "Trámite", "Revisión Préstamo"
    fecha_hora       DATETIME     NOT NULL,
    duracion_minutos INT                                                                        DEFAULT 30,

    -- Ubicación
    modalidad        ENUM ('presencial', 'virtual', 'telefonica')                               DEFAULT 'presencial',
    ubicacion        VARCHAR(255),
    enlace_virtual   VARCHAR(255),

    -- Estado
    estado           ENUM ('programada', 'confirmada', 'completada', 'cancelada', 'no_asistio') DEFAULT 'programada',

    -- Notas
    motivo           TEXT,
    observaciones    TEXT,

    -- Control
    fecha_creacion   DATETIME                                                                   DEFAULT CURRENT_TIMESTAMP,
    creado_por       VARCHAR(36),
    atendido_por     VARCHAR(36),           -- Staff que atendió

    CONSTRAINT fk_cita_sindicato
        FOREIGN KEY (sindicato_id)
            REFERENCES sindicatos (sindicato_id)
            ON DELETE CASCADE,
    CONSTRAINT fk_cita_usuario
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios (usuario_id)
            ON DELETE SET NULL,

    INDEX idx_fecha_estado (fecha_hora, estado),
    INDEX idx_usuario (usuario_id)
);
