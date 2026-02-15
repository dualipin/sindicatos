-- ============================================================================
-- SEED DE DATOS INICIALES (SIUT-ITSM)
-- Basado en la documentación oficial del Sindicato
-- ============================================================================

-- 1. SINDICATOS
-- Se usa el ID 1 explícito para mantener la integridad en las relaciones siguientes
INSERT INTO sindicatos (
    sindicato_id, nombre, abreviacion,
    direccion, telefono, correo, facebook, sitio_web,
    logo, eslogan, mision, vision, objetivo, compromiso
) VALUES (
             1,
             'Sindicato Único de Trabajadores del Instituto Tecnológico Superior de Macuspana',
             'SIUT-ITSM',
             'Av. Tecnológico s/n Lerdo de Tejada. 1ra Secc. Macuspana, Tabasco. C.P. 86719',
             '993-130-81-47', -- Teléfono principal extraído del documento
             'sindicato_siutitsm@outlook.com',
             'https://www.facebook.com/sindicatostitsm',
             'www.siutitsm.com.mx',
             'logos/ostsiutitsm.png',
             'Porque la razón está en la educación; Luchemos unidos por ella.',
             'Defender y mejorar las condiciones laborales, humanas y sociales de nuestros agremiados, promoviendo la unidad y el respeto en todo momento.',
             'Lograr consolidar a nuestro Sindicato como el mejor en el País, en materia laboral, educativa, social, política y cultural.',
             'Defender a toda costa los derechos de los trabajadores Sindicalizados, asumiendo en todo momento derechos y obligaciones como entes laborales.',
             'Nuestro compromiso es defender y mejorar las condiciones laborales, humanas y sociales de nuestros agremiados.'
         );

-- 2. PUESTOS DEL COMITÉ (Jerarquía extraída del PDF de la Asamblea)
INSERT INTO sindicato_puestos (puesto_id, sindicato_id, nombre_puesto, orden_jerarquico) VALUES
                                                                                             (1, 1, 'Secretario General', 1),
                                                                                             (2, 1, 'Secretario General Suplente', 2),
                                                                                             (3, 1, 'Secretario de Organización', 3),
                                                                                             (4, 1, 'Secretario de Trabajos y Conflictos', 4),
                                                                                             (5, 1, 'Secretario de Finanzas', 5), -- Puesto clave para préstamos
                                                                                             (6, 1, 'Secretario de Actas y Acuerdos', 6),
                                                                                             (7, 1, 'Presidente de la Comisión de Honor y Justicia', 7);

-- 3. INTEGRANTES DEL COMITÉ (Nombres reales extraídos de los documentos)
INSERT INTO sindicato_integrante_comite (sindicato_id, puesto_id, nombre, foto, activo) VALUES
                                                                                            (1, 1, 'M.I.D.S. Luiz Sosa Castro', 'integrantes/luiz_sosa.jpg', TRUE),
                                                                                            (1, 2, 'Ing. Gilberto Enrique Ascencio Suárez', 'integrantes/gilberto_ascencio.jpg', TRUE),
                                                                                            (1, 3, 'Ing. Walberto Cornelio González', 'integrantes/walberto_cornelio.jpg', TRUE),
                                                                                            (1, 4, 'Mtro. Jorge Alberto Vargas García', 'integrantes/jorge_vargas.jpg', TRUE),
                                                                                            (1, 5, 'Ing. Jesús Antonio López Hernández', 'integrantes/jesus_lopez.jpg', TRUE), -- Encargado de Finanzas
                                                                                            (1, 6, 'Roberto Morales Morales', 'integrantes/roberto_morales.jpg', TRUE),
                                                                                            (1, 7, 'Mtro. Humberto Rincón Rincón', 'integrantes/humberto_rincon.jpg', TRUE);

-- 4. VALORES INSTITUCIONALES
INSERT INTO sindicato_valores (sindicato_id, valor, orden) VALUES
                                                               (1, 'Recuperar las prestaciones en litigios de defensas de los Agremiados.', 1),
                                                               (1, 'Lograr que a ningún Administrativo y Docente sindicalizado se les adeude ningún pago devengado.', 2),
                                                               (1, 'Forjar mejores estrategias colectivas para mantener la unidad e incrementarla.', 3),
                                                               (1, 'Buscar ser un sólo Sindicato en el ITSM, buscando los canales de hermandad.', 4),
                                                               (1, 'Transparencia absoluta en el manejo de la Caja de Ahorro.', 5);

-- 5. CONFIGURACIONES DE UI (Colores)
INSERT INTO sindicato_configuraciones (sindicato_id, clave, valor, tipo) VALUES
                                                                             (1, 'color_primario', '#611232', 'color'),
                                                                             (1, 'color_secundario', '#a57f2c', 'color'),
                                                                             (1, 'color_exito', '#38b44a', 'color'),
                                                                             (1, 'color_info', '#17a2b8', 'color'),
                                                                             (1, 'color_alerta', '#efb73e', 'color'),
                                                                             (1, 'color_peligro', '#df382c', 'color');

-- 6. TASAS DE INTERÉS (Crucial para el módulo de préstamos)
-- Datos extraídos textualmente de la Solicitud de Préstamo 2026
INSERT INTO cat_tasas_interes (sindicato_id, nombre, es_agremiado, es_ahorrador, tasa_anual, activa) VALUES
                                                                                                         (1, 'Tasa Preferencial (Ahorrador Agremiado)', TRUE, TRUE, 6.00, TRUE),
                                                                                                         (1, 'Tasa Agremiado (No Ahorrador)', TRUE, FALSE, 7.50, TRUE),
                                                                                                         (1, 'Tasa Ahorrador Externo', FALSE, TRUE, 8.50, TRUE), -- Según PDF es 8.5%, no 8%
                                                                                                         (1, 'Tasa Externa General', FALSE, FALSE, 9.50, TRUE);

-- 7. TIPOS DE INGRESO (Para configuración de pagos híbridos)
INSERT INTO cat_tipos_ingreso (sindicato_id, nombre, es_periodico, frecuencia_dias, mes_pago_tentativo, activo) VALUES
                                                                                                                    (1, 'Nómina Quincenal', TRUE, 15, NULL, TRUE), -- Se descuenta cada 15 días
                                                                                                                    (1, 'Aguinaldo', FALSE, NULL, 12, TRUE),      -- Se descuenta en Diciembre
                                                                                                                    (1, 'Bono de Actuación', FALSE, NULL, 6, TRUE), -- Ejemplo genérico
                                                                                                                    (1, 'Fondo de Ahorro', FALSE, NULL, 8, TRUE);

-- ============================================================================
-- SINDICATO SECUNDARIO (DEMOSTRACIÓN MULTI-TENANCY)
-- ============================================================================

INSERT INTO sindicatos (
    nombre, abreviacion, direccion, telefono, correo,
    eslogan, mision
) VALUES (
             'Sindicato Independiente de Prueba',
             'SIP-DEMO',
             'Calle Ficticia 123, Villahermosa, Tabasco',
             '993-000-0000',
             'contacto@sipdemo.mx',
             'Unión y Fuerza',
             'Demostración del sistema multi-sindicato.'
         );

-- Obtener el ID del sindicato insertado (asumiendo que es 2)
-- Insertar configuración básica para el sindicato demo
INSERT INTO cat_tasas_interes (sindicato_id, nombre, es_agremiado, es_ahorrador, tasa_anual) VALUES
    (2, 'Estándar', TRUE, TRUE, 10.00);

INSERT INTO cat_tipos_ingreso (sindicato_id, nombre, es_periodico, frecuencia_dias) VALUES
    (2, 'Nómina Semanal', TRUE, 7);