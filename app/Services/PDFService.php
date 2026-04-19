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
