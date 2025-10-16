<?php
/**
 * Script de diagnóstico para verificar la configuración de locale del servidor
 * 
 * Este archivo te ayudará a identificar problemas de configuración entre
 * tu servidor local y el servidor de producción (Latin Host)
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta public/ de tu servidor
 * 2. Accede a él desde el navegador: https://tudominio.com/diagnostico-locale.php
 * 3. Compara los resultados con tu servidor local
 * 4. Una vez identificado el problema, ELIMINA este archivo por seguridad
 */

// Prevenir acceso no autorizado (opcional - comenta estas líneas si necesitas acceder)
// if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
//     die('Acceso no autorizado');
// }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Configuración de Locale</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
            border-left: 4px solid #3498db;
            padding-left: 10px;
        }
        .test-group {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .error {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fffef5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #3498db;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .code {
            background: #2c3e50;
            color: #2ecc71;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-info { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico de Configuración del Servidor</h1>
        <p><strong>Fecha y Hora:</strong> <?= date('Y-m-d H:i:s') ?></p>
        <p><strong>Servidor:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido' ?></p>

        <!-- INFORMACIÓN DE PHP -->
        <h2>📦 Información de PHP</h2>
        <div class="test-group">
            <table>
                <tr>
                    <th>Configuración</th>
                    <th>Valor</th>
                </tr>
                <tr>
                    <td>Versión de PHP</td>
                    <td><span class="badge badge-info"><?= PHP_VERSION ?></span></td>
                </tr>
                <tr>
                    <td>Sistema Operativo</td>
                    <td><?= PHP_OS ?></td>
                </tr>
                <tr>
                    <td>Arquitectura</td>
                    <td><?= php_uname() ?></td>
                </tr>
            </table>
        </div>

        <!-- CONFIGURACIÓN DE LOCALE -->
        <h2>🌍 Configuración de Locale Actual</h2>
        <div class="test-group">
            <table>
                <tr>
                    <th>Categoría</th>
                    <th>Valor Actual</th>
                </tr>
                <tr>
                    <td>LC_ALL (Todo)</td>
                    <td><code><?= setlocale(LC_ALL, 0) ?></code></td>
                </tr>
                <tr>
                    <td>LC_NUMERIC (Números)</td>
                    <td><code><?= setlocale(LC_NUMERIC, 0) ?></code></td>
                </tr>
                <tr>
                    <td>LC_TIME (Tiempo)</td>
                    <td><code><?= setlocale(LC_TIME, 0) ?></code></td>
                </tr>
                <tr>
                    <td>LC_MONETARY (Moneda)</td>
                    <td><code><?= setlocale(LC_MONETARY, 0) ?></code></td>
                </tr>
                <tr>
                    <td>LC_CTYPE (Caracteres)</td>
                    <td><code><?= setlocale(LC_CTYPE, 0) ?></code></td>
                </tr>
            </table>
        </div>

        <!-- PRUEBAS DE FORMATEO DE NÚMEROS -->
        <h2>🔢 Pruebas de Formateo de Números</h2>
        <?php
        $testValues = [8000, 80000, 8000600065000550000];
        $currentLocale = setlocale(LC_ALL, 0);
        ?>
        
        <div class="test-group <?= (setlocale(LC_NUMERIC, 0) !== 'C') ? 'warning' : '' ?>">
            <h3>Con Configuración Actual (<?= setlocale(LC_NUMERIC, 0) ?>)</h3>
            <table>
                <tr>
                    <th>Valor Original</th>
                    <th>number_format()</th>
                    <th>Resultado Esperado</th>
                    <th>Estado</th>
                </tr>
                <?php foreach ($testValues as $val): ?>
                    <?php 
                    $formatted = number_format($val, 0, '.', ',');
                    $expected = ($val == 8000) ? '$ 8,000' : 
                               (($val == 80000) ? '$ 80,000' : '$ 8,000,600,065,000,550,000');
                    $isCorrect = ($formatted == str_replace('$ ', '', $expected));
                    ?>
                    <tr>
                        <td><?= $val ?></td>
                        <td><strong>$ <?= $formatted ?></strong></td>
                        <td><?= $expected ?></td>
                        <td>
                            <?php if ($isCorrect): ?>
                                <span class="badge badge-success">✓ Correcto</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ Incorrecto</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- PRUEBA CON LC_NUMERIC = 'C' -->
        <?php
        setlocale(LC_NUMERIC, 'C');
        ?>
        <div class="test-group">
            <h3>Con LC_NUMERIC = 'C' (Recomendado)</h3>
            <table>
                <tr>
                    <th>Valor Original</th>
                    <th>number_format()</th>
                    <th>Resultado Esperado</th>
                    <th>Estado</th>
                </tr>
                <?php foreach ($testValues as $val): ?>
                    <?php 
                    $formatted = number_format($val, 0, '.', ',');
                    $expected = ($val == 8000) ? '$ 8,000' : 
                               (($val == 80000) ? '$ 80,000' : '$ 8,000,600,065,000,550,000');
                    $isCorrect = ($formatted == str_replace('$ ', '', $expected));
                    ?>
                    <tr>
                        <td><?= $val ?></td>
                        <td><strong>$ <?= $formatted ?></strong></td>
                        <td><?= $expected ?></td>
                        <td>
                            <?php if ($isCorrect): ?>
                                <span class="badge badge-success">✓ Correcto</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ Incorrecto</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- LOCALES DISPONIBLES -->
        <h2>📋 Locales Disponibles en el Sistema</h2>
        <div class="test-group">
            <?php
            // Intentar obtener locales disponibles
            $locales_to_test = [
                'es_CO.UTF-8',
                'es_CO',
                'es_ES.UTF-8',
                'es_ES',
                'es',
                'C',
                'POSIX',
                'en_US.UTF-8',
                'en_US'
            ];
            ?>
            <table>
                <tr>
                    <th>Locale</th>
                    <th>Disponible</th>
                </tr>
                <?php foreach ($locales_to_test as $locale): ?>
                    <?php 
                    $current = setlocale(LC_ALL, 0);
                    $result = setlocale(LC_ALL, $locale);
                    $available = ($result !== false);
                    setlocale(LC_ALL, $current); // Restaurar
                    ?>
                    <tr>
                        <td><code><?= $locale ?></code></td>
                        <td>
                            <?php if ($available): ?>
                                <span class="badge badge-success">✓ Disponible</span>
                            <?php else: ?>
                                <span class="badge badge-danger">✗ No disponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- INFORMACIÓN ADICIONAL -->
        <h2>ℹ️ Información Adicional</h2>
        <div class="test-group">
            <table>
                <tr>
                    <th>Configuración</th>
                    <th>Valor</th>
                </tr>
                <tr>
                    <td>Zona Horaria PHP</td>
                    <td><?= date_default_timezone_get() ?></td>
                </tr>
                <tr>
                    <td>Extensión Intl</td>
                    <td>
                        <?php if (extension_loaded('intl')): ?>
                            <span class="badge badge-success">✓ Instalada</span>
                        <?php else: ?>
                            <span class="badge badge-warning">✗ No instalada</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Extensión BCMath</td>
                    <td>
                        <?php if (extension_loaded('bcmath')): ?>
                            <span class="badge badge-success">✓ Instalada</span>
                        <?php else: ?>
                            <span class="badge badge-warning">✗ No instalada</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- RECOMENDACIONES -->
        <h2>💡 Recomendaciones</h2>
        <div class="test-group">
            <?php
            $hasIssues = false;
            $recommendations = [];

            if (setlocale(LC_NUMERIC, 0) !== 'C') {
                $hasIssues = true;
                $recommendations[] = "⚠️ <strong>LC_NUMERIC no está configurado como 'C'.</strong> Esto puede causar problemas de formateo de números.";
            }

            if (!extension_loaded('intl')) {
                $recommendations[] = "ℹ️ La extensión <strong>Intl</strong> no está instalada. Se recomienda instalarla para mejor manejo de internacionalización.";
            }

            if (!extension_loaded('bcmath')) {
                $recommendations[] = "ℹ️ La extensión <strong>BCMath</strong> no está instalada. Tu aplicación la usa en la función rounded().";
            }

            if (empty($recommendations)) {
                echo '<p style="color: #28a745;">✅ <strong>¡Todo parece estar configurado correctamente!</strong></p>';
            } else {
                echo '<ul>';
                foreach ($recommendations as $rec) {
                    echo '<li>' . $rec . '</li>';
                }
                echo '</ul>';
            }
            ?>
        </div>

        <!-- SOLUCIÓN IMPLEMENTADA -->
        <h2>✅ Solución Implementada en la Aplicación</h2>
        <div class="test-group">
            <p>La aplicación ahora está configurada para forzar <code>LC_NUMERIC = 'C'</code> en el archivo <code>AppServiceProvider.php</code>:</p>
            <div class="code">
setlocale(LC_NUMERIC, 'C');<br>
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es_CO.UTF-8', 'es_CO', 'es');<br>
setlocale(LC_MONETARY, 'es_CO.UTF-8', 'es_CO', 'es_ES.UTF-8', 'es_ES', 'es');
            </div>
            <p>Esta configuración asegura que:</p>
            <ul>
                <li>Los números se formateen consistentemente con punto (.) como separador decimal</li>
                <li>Las fechas y moneda usen formato colombiano/español</li>
                <li>No haya diferencias entre servidor local y producción</li>
            </ul>
        </div>

        <!-- ADVERTENCIA DE SEGURIDAD -->
        <div class="test-group error">
            <h3>🔒 IMPORTANTE - Seguridad</h3>
            <p><strong>⚠️ ELIMINA este archivo después de hacer el diagnóstico.</strong></p>
            <p>Este archivo expone información sobre la configuración de tu servidor que podría ser útil para atacantes.</p>
        </div>
    </div>
</body>
</html>

