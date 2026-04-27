<?php
/** @var \App\Core\Router $router */

// Landing page pública (página inicial)
$router->get('/',        'LandingController@index');
$router->get('/landing', 'LandingController@index');

// Auth
$router->get('/login',  'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Atendimentos
$router->get('/atendimentos',                       'AtendimentoController@index');
$router->get('/atendimentos/novo',                  'AtendimentoController@create');
$router->post('/atendimentos',                      'AtendimentoController@store');
$router->get('/atendimentos/{id}',                  'AtendimentoController@show');
$router->post('/atendimentos/{id}/analisar-ia',     'AtendimentoController@analisarIA');
$router->post('/atendimentos/{id}/documento',       'AtendimentoController@gerarDocumento');
$router->get('/atendimentos/{id}/download/{docId}', 'AtendimentoController@downloadDocumento');
$router->post('/atendimentos/{id}/status',          'AtendimentoController@updateStatus');

// Medidas de Proteção
$router->get('/medidas',                          'MedidaController@index');
$router->get('/medidas/eca',                      'MedidaController@getMedidasECA');
$router->post('/atendimentos/{id}/medidas',       'MedidaController@store');
$router->post('/medidas/{id}/status',             'MedidaController@updateStatus');

// Rede de Serviços
$router->get('/rede-servicos',         'RedeServicosController@index');
$router->post('/rede-servicos',        'RedeServicosController@store');
$router->post('/rede-servicos/{id}',   'RedeServicosController@update');
$router->post('/rede-servicos/{id}/delete', 'RedeServicosController@destroy');

// Cadastro público
$router->get('/cadastro',  'LandingController@cadastroForm');
$router->post('/cadastro', 'LandingController@cadastroStore');

// Páginas legais públicas
$router->get('/privacidade',   'LegalController@privacidade');
$router->get('/termos-de-uso', 'LegalController@termos');

// Admin (Super Admin)
$router->get('/admin',                              'AdminController@dashboard');
$router->post('/admin/tenants',                     'AdminController@storeTenant');
$router->post('/admin/users',                       'AdminController@storeUser');
$router->post('/admin/purge',                       'AdminController@purgeDocuments');
$router->post('/admin/users/{id}/toggle',           'AdminController@toggleUser');
$router->post('/admin/cadastros/{id}/aprovar',      'AdminController@aprovarCadastro');
$router->post('/admin/configs',                     'AdminController@saveConfigs');
$router->post('/admin/chamados/{id}/responder',     'AdminController@responderChamado');
$router->post('/admin/paginas-legais',              'AdminController@saveLegalPage');

// Medidas de Campo (documentos avulsos gerados em campo)
$router->get('/medidas-campo',                          'MedidaCampoController@index');
$router->get('/medidas-campo/nova',                     'MedidaCampoController@nova');
$router->post('/medidas-campo',                         'MedidaCampoController@store');
$router->get('/medidas-campo/{id}',                     'MedidaCampoController@show');
$router->post('/medidas-campo/{id}/gerar-texto',        'MedidaCampoController@gerarTexto');
$router->post('/medidas-campo/{id}/assinar',            'MedidaCampoController@assinar');
$router->post('/medidas-campo/{id}/salvar-texto',       'MedidaCampoController@salvarTexto');
$router->get('/medidas-campo/{id}/download',            'MedidaCampoController@download');

// Agenda
$router->get('/agenda',                  'AgendaController@index');
$router->post('/agenda',                 'AgendaController@store');
$router->post('/agenda/{id}/status',     'AgendaController@updateStatus');
$router->post('/agenda/{id}/delete',     'AgendaController@destroy');

// Chamados de Suporte (painel do assinante)
$router->get('/chamados',  'ChamadoController@index');
$router->post('/chamados', 'ChamadoController@store');
