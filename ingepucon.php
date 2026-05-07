<?php

// INGEPUCON — página de marca. Usa el handler POST de index.php para leads.
if (getenv('DEV_PREVIEW') === '1') {
    require __DIR__ . '/lib/dev-stubs.php';
} else {
    require __DIR__ . '/lib/bootstrap.php';
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_lead') {
    require __DIR__ . '/index.php';
    return;
}

$brand = [
    'key'             => 'ingcon',
    'name'            => 'INGEPUCON',
    'kind'            => 'Ingeniería',
    'eyebrow'         => '02 — Ingeniería',
    'tagline'         => 'Línea de ingeniería y cálculo estructural. Proyectos técnicos, cálculos y asesorías especializadas para arquitectos, constructoras y propietarios — bajo norma chilena vigente.',
    'description'     => 'INGEPUCON — línea de ingeniería estructural de Alexis Bello. Cálculo de hormigón armado, acero y madera bajo NCh 433/430/427/2369 en La Araucanía.',
    'accent'          => '#1F3556',
    'accent_dk'       => '#14233B',
    'hero_bg'         => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1800&q=85&auto=format&fit=crop',
    'chips'           => ['NCh 433 · 430 · 427 · 2369', 'Memoria firmada', 'Hormigón · Acero · Madera', 'Asesoría técnica', 'Regularizaciones'],

    'services_title'  => 'Cálculo estructural. <em>Certeza técnica.</em>',
    'services_lede'   => 'Diseño estructural completo, memorias firmadas y peritajes — bajo norma chilena vigente.',
    'services'        => [
        ['title' => 'Cálculo estructural',  'text' => 'Diseño y cálculo de estructuras de hormigón armado, acero y madera. Memoria de cálculo y planos firmados conforme a NCh 433, 430, 427 y 2369.'],
        ['title' => 'Diseño estructural',   'text' => 'Predimensionamiento y modelación de estructuras desde anteproyecto. Coordinación con arquitectura.'],
        ['title' => 'Ingeniería de proyectos', 'text' => 'Coordinación multidisciplinaria de especialidades hasta entrega completa de proyecto.'],
        ['title' => 'Asesoría técnica',     'text' => 'Revisión de proyectos de terceros, segunda opinión profesional y peritajes estructurales.'],
        ['title' => 'Regularizaciones',     'text' => 'Cálculo retroactivo y memoria para regularización de construcciones existentes ante la DOM.'],
        ['title' => 'Patologías estructurales', 'text' => 'Inspección, diagnóstico e informe técnico sobre patologías y daños en estructuras existentes.'],
    ],

    'projects'        => [
        ['title' => 'Cálculo casa Lican Ray', 'type' => 'Habitacional · 180 m²', 'text' => 'Cálculo estructural en hormigón armado y madera laminada. Memoria + planos firmados.', 'img' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200&q=85&auto=format&fit=crop'],
        ['title' => 'Galpón industrial',      'type' => 'Industrial · 600 m²',  'text' => 'Estructura metálica calculada bajo NCh 2369. Diseño sismorresistente para zona 3.', 'img' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=1200&q=85&auto=format&fit=crop'],
        ['title' => 'Regularización vivienda', 'type' => 'Regularización',       'text' => 'Cálculo retroactivo y memoria para regularización ante DOM Pucón. Aprobado a primera vuelta.', 'img' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200&q=85&auto=format&fit=crop'],
    ],

    'why_title'       => '¿Por qué <em>INGEPUCON?</em>',
    'why'             => [
        ['title' => 'Bajo norma vigente', 'text' => 'NCh 433, 430, 427 y 2369. Sin atajos. Sin interpretaciones libres.'],
        ['title' => 'Trato directo',      'text' => 'Hablás con el calculista, no con un intermediario. Respuestas técnicas reales.'],
        ['title' => 'Memoria sólida',     'text' => 'Documentación completa, planos firmados y respaldo digital de cada proyecto.'],
        ['title' => 'Coordinación real',  'text' => 'Trabajamos coordinados con arquitectura y constructora. Sin fricción innecesaria.'],
    ],

    'form_options'    => [
        'Cálculo estructural',
        'Ingeniería de proyecto',
        'Asesoría técnica',
        'Regularización',
        'Peritaje / patología',
    ],
];

require __DIR__ . '/components/ingcon_template.php';
