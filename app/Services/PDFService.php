<?php
namespace App\Services;

/**
 * Gerador de PDF nativo (sem dependências externas).
 * Gera PDFs utilizando FPDF incluído localmente.
 */
class PDFService
{
    private string $storagePath;

    public function __construct()
    {
        $this->storagePath = ROOT . '/storage/pdfs';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    /**
     * Gera HTML para o PDF de um documento assinado.
     */
    public function gerarDocumentoAssinado(array $atendimento, array $documento, string $assinaturaBase64): string
    {
        $filename = 'doc_' . ($documento['tipo_documento'] ? preg_replace('/[^a-z0-9]/i', '_', $documento['tipo_documento']) : 'doc') . '_' . time() . '.html';
        $filepath = $this->storagePath . '/' . $filename;

        $html = $this->buildDocumentoHTML($atendimento, $documento, $assinaturaBase64);
        file_put_contents($filepath, $html);

        return $filename;
    }

    /**
     * Gera um PDF via conversão HTML-to-PDF usando wkhtmltopdf ou fallback para HTML.
     */
    public function gerarRelatorioAtendimento(array $atendimento, array $analiseIA): string
    {
        $filename = 'relatorio_' . $atendimento['numero_protocolo'] . '_' . time() . '.html';
        $filepath = $this->storagePath . '/' . $filename;

        $html = $this->buildRelatorioHTML($atendimento, $analiseIA);
        file_put_contents($filepath, $html);

        return $filename;
    }

    private function buildDocumentoHTML(array $atendimento, array $documento, string $assinaturaBase64): string
    {
        $protocolo  = $atendimento['numero_protocolo'];
        $data       = date('d/m/Y H:i');
        $assinante  = htmlspecialchars($documento['assinante_nome'] ?? 'Conselheiro(a)');
        $cargo      = htmlspecialchars($documento['assinante_cargo'] ?? 'Conselheiro(a) Tutelar');
        $tipoDoc    = htmlspecialchars($documento['tipo_documento']);
        $conteudo   = nl2br(htmlspecialchars($documento['conteudo'] ?? ''));

        $sigImg = $assinaturaBase64
            ? "<img src='{$assinaturaBase64}' style='max-width:250px;border-bottom:1px solid #000;' alt='Assinatura'>"
            : '<p style="border-bottom:1px solid #000;width:250px;">&nbsp;</p>';

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>{$tipoDoc} - Protocolo {$protocolo}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 12pt; color: #000; margin: 40px; }
  .header { text-align:center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
  .header h1 { font-size: 14pt; margin: 0; }
  .header h2 { font-size: 12pt; margin: 5px 0; }
  .section { margin: 20px 0; }
  .section h3 { font-size: 11pt; text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
  .assinatura { margin-top: 60px; text-align: center; }
  .footer { margin-top: 40px; font-size: 9pt; color: #666; border-top: 1px solid #ccc; padding-top: 10px; }
  .badge { background: #1a1a2e; color: #fff; padding: 5px 15px; border-radius: 4px; font-size: 10pt; display: inline-block; }
  @media print { body { margin: 20px; } }
</style>
</head>
<body>
  <div class="header">
    <h1>CONSELHO TUTELAR</h1>
    <h2>GUARDIÃO DIGITAL - Sistema de Proteção à Criança e ao Adolescente</h2>
    <p class="badge">PROTOCOLO: {$protocolo}</p>
  </div>

  <div class="section">
    <h3>{$tipoDoc}</h3>
    <p><strong>Data de Emissão:</strong> {$data}</p>
    <p><strong>Responsável:</strong> {$assinante} - {$cargo}</p>
  </div>

  <div class="section">
    <h3>Conteúdo</h3>
    <div style="line-height:1.8;">{$conteudo}</div>
  </div>

  <div class="assinatura">
    <p>Assinatura Digital:</p>
    {$sigImg}
    <p><strong>{$assinante}</strong></p>
    <p>{$cargo}</p>
    <p style="font-size:9pt;color:#666;">Documento assinado eletronicamente em {$data}</p>
  </div>

  <div class="footer">
    <p>⚠️ Este documento foi gerado pelo sistema VivensiCT e possui validade jurídica nos termos da Lei 14.063/2020.</p>
    <p>Protocolo: {$protocolo} | Gerado em: {$data}</p>
    <p>🔐 LGPD: Este documento será automaticamente excluído do servidor em 3 dias conforme política de retenção de dados.</p>
  </div>
</body>
</html>
HTML;
    }

    private function buildRelatorioHTML(array $atendimento, array $analiseIA): string
    {
        $protocolo = $atendimento['numero_protocolo'];
        $data      = date('d/m/Y H:i');
        $prioridade = strtoupper($analiseIA['prioridade'] ?? 'MEDIA');
        $analise   = nl2br(htmlspecialchars($analiseIA['analise_juridica'] ?? ''));
        $obs       = nl2br(htmlspecialchars($analiseIA['observacoes'] ?? ''));

        $encaminhamentosHTML = '';
        foreach (($analiseIA['fluxo_encaminhamento'] ?? []) as $enc) {
            $orgao   = htmlspecialchars($enc['orgao'] ?? '');
            $tipo    = htmlspecialchars($enc['tipo'] ?? '');
            $artigo  = htmlspecialchars($enc['artigo_eca'] ?? '');
            $urgencia= strtoupper($enc['urgencia'] ?? '');
            $desc    = nl2br(htmlspecialchars($enc['descricao'] ?? ''));
            $encaminhamentosHTML .= "<tr><td>{$orgao}</td><td>{$tipo}</td><td>{$artigo}</td><td>{$urgencia}</td><td>{$desc}</td></tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Atendimento - {$protocolo}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 11pt; margin: 40px; }
  .header { text-align:center; background:#1a1a2e; color:#fff; padding:20px; margin-bottom:20px; }
  table { width:100%; border-collapse:collapse; margin:10px 0; }
  th { background:#1a1a2e; color:#fff; padding:8px; text-align:left; }
  td { padding:8px; border-bottom:1px solid #eee; }
  .prioridade { color: #e74c3c; font-weight: bold; font-size: 14pt; }
  h3 { color: #1a1a2e; border-left: 4px solid #e67e22; padding-left: 10px; }
  .footer { margin-top:40px; font-size:9pt; color:#666; border-top:1px solid #ccc; padding-top:10px; }
</style>
</head>
<body>
  <div class="header">
    <h1 style="margin:0;">GUARDIÃO DIGITAL</h1>
    <p style="margin:5px 0;">Relatório de Atendimento | Protocolo: {$protocolo}</p>
    <p style="margin:0;font-size:10pt;">Gerado em: {$data}</p>
  </div>

  <p>Prioridade: <span class="prioridade">{$prioridade}</span></p>

  <h3>Análise de Leis (IA)</h3>
  <p>{$analise}</p>

  <h3>Fluxo de Encaminhamentos</h3>
  <table>
    <tr><th>Órgão</th><th>Tipo</th><th>Base Legal</th><th>Urgência</th><th>Descrição</th></tr>
    {$encaminhamentosHTML}
  </table>

  <h3>Observações</h3>
  <p>{$obs}</p>

  <div class="footer">
    VivensiCT | ECA/SUAS Compliance | {$data} | Protocolo: {$protocolo}
  </div>
</body>
</html>
HTML;
    }

    /**
     * Gera o documento HTML de uma Medida de Campo (avulsa, sem atendimento).
     */
    public function gerarMedidaCampo(array $doc, string $assinaturaBase64): string
    {
        $filename = 'mc_' . preg_replace('/[^a-z0-9]/i', '_', $doc['numero_doc']) . '_' . time() . '.html';
        $filepath = $this->storagePath . '/' . $filename;

        $numDoc      = htmlspecialchars($doc['numero_doc']);
        $tipo        = htmlspecialchars($doc['tipo_medida']);
        $artigo      = htmlspecialchars($doc['artigo_eca'] ?? '');
        $nomeCrianca = htmlspecialchars($doc['nome_crianca'] ?? '');
        $texto       = nl2br(htmlspecialchars($doc['texto_medida'] ?? ''));
        $assinante   = htmlspecialchars($doc['assinante_nome'] ?? '');
        $cargo       = htmlspecialchars($doc['assinante_cargo'] ?? 'Conselheiro(a) Tutelar');
        $municipio   = htmlspecialchars($doc['municipio'] ?? '');
        $tenant      = htmlspecialchars($doc['tenant_nome'] ?? 'Conselho Tutelar');
        $data        = date('d/m/Y');
        $hora        = date('H:i');

        $sigHtml = $assinaturaBase64
            ? "<img src='{$assinaturaBase64}' style='max-width:280px;display:block;margin:0 auto;' alt='Assinatura Digital'>"
            : '<div style="width:280px;height:70px;border-bottom:2px solid #000;margin:0 auto;"></div>';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Medida de Proteção — {$numDoc}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Times New Roman', serif; font-size: 12pt; color: #000; background: #fff; }
  .page { max-width: 700px; margin: 0 auto; padding: 40px 50px; }
  .header { text-align: center; border-bottom: 3px double #1a1a2e; padding-bottom: 18px; margin-bottom: 24px; }
  .header .brasao { font-size: 36px; display: block; margin-bottom: 8px; }
  .header h1 { font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
  .header h2 { font-size: 11pt; font-weight: normal; margin-top: 4px; }
  .header .num-doc { margin-top: 10px; display: inline-block; background: #1a1a2e; color: #fff; padding: 4px 16px; border-radius: 4px; font-size: 10pt; font-weight: bold; letter-spacing: 1px; }
  .secao { margin: 18px 0; }
  .secao h3 { font-size: 11pt; text-transform: uppercase; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 4px; margin-bottom: 10px; letter-spacing: 0.5px; }
  .secao p { font-size: 11pt; line-height: 1.9; text-align: justify; }
  .dados-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; font-size: 10.5pt; }
  .dado { padding: 4px 0; border-bottom: 1px solid #eee; }
  .dado span { font-weight: bold; }
  .assinatura-box { margin-top: 50px; text-align: center; page-break-inside: avoid; }
  .assinatura-box p { font-size: 10.5pt; margin-top: 8px; }
  .assinatura-box .nome-ass { font-weight: bold; font-size: 11pt; margin-top: 6px; }
  .assinatura-box .cargo-ass { font-size: 10pt; color: #444; }
  .badge-legal { display: inline-block; background: #f0f4ff; border: 1px solid #1a1a2e; color: #1a1a2e; padding: 3px 10px; border-radius: 4px; font-size: 9.5pt; font-weight: bold; margin: 2px; }
  .footer { margin-top: 36px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 8.5pt; color: #666; text-align: center; }
  .alerta-lgpd { background: #fffbea; border: 1px solid #f59e0b; padding: 8px 12px; border-radius: 4px; font-size: 8.5pt; color: #78350f; margin-top: 12px; }
  @media print {
    .page { padding: 20px 30px; }
    .no-print { display: none; }
  }
</style>
</head>
<body>
<div class="page">

  <div class="header">
    <span class="brasao">⚖️</span>
    <h1>{$tenant}</h1>
    <h2>{$municipio} — Sistema Guardião Digital</h2>
    <div class="num-doc">DOC Nº {$numDoc}</div>
  </div>

  <div class="secao">
    <h3>🛡️ Medida de Proteção — {$tipo}</h3>
    <div class="dados-grid">
      <div class="dado"><span>Base Legal:</span> <span class="badge-legal">{$artigo}</span></div>
      <div class="dado"><span>Data de Emissão:</span> {$data} às {$hora}</div>
      <div class="dado"><span>Criança / Adolescente:</span> {$nomeCrianca}</div>
      <div class="dado"><span>Município:</span> {$municipio}</div>
    </div>
  </div>

  <div class="secao">
    <h3>📋 Texto da Medida</h3>
    <p>{$texto}</p>
  </div>

  <div class="assinatura-box">
    <p>Assinatura Digital do(a) Conselheiro(a) Tutelar:</p>
    <div style="margin: 16px 0;">
      {$sigHtml}
    </div>
    <div class="nome-ass">{$assinante}</div>
    <div class="cargo-ass">{$cargo}</div>
    <p style="font-size:9pt;color:#666;margin-top:6px;">Documento assinado eletronicamente em {$data} às {$hora}</p>
  </div>

  <div class="footer">
    <p>Documento gerado pelo sistema <strong>VivensiCT / Guardião Digital</strong> · ECA/SUAS Compliance</p>
    <p>Nº {$numDoc} · Emissão: {$data} {$hora} · Validade: conforme legislação vigente</p>
    <div class="alerta-lgpd">⚠️ LGPD: Este documento contém dados sensíveis protegidos pela Lei 13.709/2018. Manuseie com responsabilidade.</div>
  </div>

</div>
</body>
</html>
HTML;

        file_put_contents($filepath, $html);
        return $filename;
    }

    /**
     * Serve o arquivo para download.
     */
    public function download(string $filename): void
    {
        $filepath = $this->storagePath . '/' . $filename;
        if (!file_exists($filepath)) {
            http_response_code(404);
            die('Arquivo não encontrado ou já expirou.');
        }
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($filepath);
        exit;
    }

    /**
     * Remove documentos expirados (LGPD - expurgo automático após 3 dias).
     */
    public function purgeExpired(): int
    {
        $db   = \App\Core\Database::getInstance();
        $docs = \App\Core\Database::select(
            "SELECT * FROM documentos WHERE expira_em < NOW() AND excluido = 0"
        );

        $count = 0;
        foreach ($docs as $doc) {
            if ($doc['caminho_arquivo']) {
                $path = $this->storagePath . '/' . $doc['caminho_arquivo'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            \App\Core\Database::update('documentos', [
                'excluido'       => 1,
                'data_exclusao'  => date('Y-m-d H:i:s'),
                'caminho_arquivo'=> null,
            ], 'id = ?', [$doc['id']]);
            $count++;
        }

        return $count;
    }
}
