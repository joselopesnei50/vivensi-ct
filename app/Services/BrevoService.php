<?php
namespace App\Services;

use App\Core\Database;

class BrevoService
{
    private static function apiKey(): string
    {
        try {
            $row = Database::selectOne("SELECT valor FROM api_configs WHERE chave = 'brevo_key'");
            return $row['valor'] ?? '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    public static function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $apiKey = self::apiKey();
        if (empty(trim($apiKey))) return false;

        $payload = json_encode([
            'sender'      => ['name' => 'VivensiCT', 'email' => 'noreply@vivensict.com.br'],
            'to'          => [['email' => $toEmail, 'name' => $toName]],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ]);

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'api-key: ' . $apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_exec($ch);
        curl_close($ch);

        return $code >= 200 && $code < 300;
    }

    public static function sendRegistrationEmail(string $nome, string $email): bool
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<body style="font-family:Inter,Arial,sans-serif;background:#f0f4ff;margin:0;padding:32px;">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
  <div style="background:linear-gradient(135deg,#020c1b 0%,#1d4ed8 100%);padding:36px;text-align:center;">
    <p style="font-size:40px;margin:0 0 8px;">🛡️</p>
    <h1 style="color:#fff;font-size:22px;margin:0;font-weight:800;letter-spacing:-0.5px;">VivensiCT</h1>
    <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:6px 0 0;">Sistema ECA/SUAS para Conselheiros Tutelares</p>
  </div>
  <div style="padding:36px;">
    <h2 style="color:#020c1b;font-size:20px;margin:0 0 14px;font-weight:800;">Cadastro recebido com sucesso ✅</h2>
    <p style="color:#5b78a8;font-size:14px;line-height:1.75;margin:0 0 24px;">
      Olá, <strong style="color:#020c1b;">{$nome}</strong>!<br><br>
      Recebemos seu pedido de acesso ao <strong>VivensiCT</strong>. Nossa equipe irá analisar seus dados
      e em breve você receberá as instruções de acesso via e-mail e WhatsApp.
    </p>
    <div style="background:#f0f4ff;border-radius:12px;padding:20px 24px;margin-bottom:28px;">
      <p style="margin:0 0 10px;font-size:13px;font-weight:700;color:#1e3a5f;">O que acontece agora?</p>
      <ol style="margin:0;padding-left:20px;color:#5b78a8;font-size:13px;line-height:2;">
        <li>Nossa equipe revisa e aprova seu cadastro</li>
        <li>Criamos seu acesso personalizado ao sistema</li>
        <li>Você recebe login + senha via WhatsApp e e-mail</li>
      </ol>
    </div>
    <div style="background:#d1fae5;border:1px solid #a7f3d0;border-radius:10px;padding:14px 20px;margin-bottom:28px;">
      <p style="margin:0;font-size:13px;color:#065f46;">
        ⚡ <strong>Prazo:</strong> A aprovação ocorre em até 24 horas úteis.
      </p>
    </div>
    <p style="font-size:11px;color:#93a8cc;text-align:center;margin:0;line-height:1.6;">
      © 2025 VivensiCT · Projeto Social · Proteção LGPD — Lei 13.709/18<br>
      Este e-mail foi enviado automaticamente. Não responda a esta mensagem.
    </p>
  </div>
</div>
</body>
</html>
HTML;
        return self::send($email, $nome, '✅ Cadastro recebido — VivensiCT', $html);
    }

    public static function sendApprovalEmail(string $nome, string $email, string $loginUrl): bool
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<body style="font-family:Inter,Arial,sans-serif;background:#f0f4ff;margin:0;padding:32px;">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
  <div style="background:linear-gradient(135deg,#065f46 0%,#10b981 100%);padding:36px;text-align:center;">
    <p style="font-size:40px;margin:0 0 8px;">🎉</p>
    <h1 style="color:#fff;font-size:22px;margin:0;font-weight:800;">Acesso Aprovado!</h1>
    <p style="color:rgba(255,255,255,0.7);font-size:13px;margin:6px 0 0;">Seu Conselho Tutelar está no VivensiCT</p>
  </div>
  <div style="padding:36px;">
    <h2 style="color:#020c1b;font-size:20px;margin:0 0 14px;font-weight:800;">Bem-vindo(a), {$nome}! 🛡️</h2>
    <p style="color:#5b78a8;font-size:14px;line-height:1.75;margin:0 0 24px;">
      Seu cadastro foi <strong style="color:#10b981;">aprovado</strong> pela equipe VivensiCT.
      Você já pode acessar o sistema com as credenciais enviadas via WhatsApp.
    </p>
    <div style="text-align:center;margin:28px 0;">
      <a href="{$loginUrl}" style="background:linear-gradient(135deg,#020c1b,#1d4ed8);color:#fff;text-decoration:none;padding:16px 40px;border-radius:12px;font-weight:800;font-size:15px;display:inline-block;letter-spacing:-0.2px;">
        🚀 Acessar o Sistema
      </a>
    </div>
    <p style="font-size:11px;color:#93a8cc;text-align:center;margin:0;line-height:1.6;">
      © 2025 VivensiCT · Sistema ECA/SUAS · Proteção LGPD
    </p>
  </div>
</div>
</body>
</html>
HTML;
        return self::send($email, $nome, '🎉 Acesso aprovado — VivensiCT', $html);
    }
}
