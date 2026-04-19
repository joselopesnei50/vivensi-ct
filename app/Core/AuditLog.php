<?php
namespace App\Core;

/**
 * AuditLog — LGPD-compliant activity recorder.
 * All writes are fire-and-forget; failures are silently ignored so that
 * a logging hiccup never breaks the user-facing request.
 */
class AuditLog
{
    /**
     * Record an auditable event.
     *
     * @param string   $acao        Human-readable action, e.g. 'login', 'atendimento.create'
     * @param string|null $tabela   Affected table name
     * @param int|null $registroId  Primary key of the affected row
     * @param array    $dadosNovos  New values (will be JSON-encoded; omit PII or encrypt before passing)
     * @param array    $dadosAnt    Previous values (same note)
     */
    public static function record(
        string  $acao,
        ?string $tabela     = null,
        ?int    $registroId = null,
        array   $dadosNovos = [],
        array   $dadosAnt   = []
    ): void {
        try {
            Database::insert('audit_logs', [
                'user_id'          => Auth::id(),
                'tenant_id'        => Auth::tenantId(),
                'acao'             => $acao,
                'tabela'           => $tabela,
                'registro_id'      => $registroId,
                'dados_anteriores' => $dadosAnt  ? json_encode($dadosAnt,  JSON_UNESCAPED_UNICODE) : null,
                'dados_novos'      => $dadosNovos ? json_encode($dadosNovos, JSON_UNESCAPED_UNICODE) : null,
                'ip_address'       => Request::ip(),
                'user_agent'       => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (\Throwable) {
            // Never let a logging failure break the application
        }
    }

    /** Shorthand for login/logout events (no tabela/registro needed). */
    public static function auth(string $event, ?int $userId = null): void
    {
        try {
            Database::insert('audit_logs', [
                'user_id'    => $userId ?? Auth::id(),
                'tenant_id'  => Auth::tenantId(),
                'acao'       => $event,
                'ip_address' => Request::ip(),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (\Throwable) {}
    }
}
