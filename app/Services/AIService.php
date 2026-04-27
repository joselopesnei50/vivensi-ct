<?php
namespace App\Services;

class AIService
{
    private string $apiKey;
    private string $provider;
    private string $model;
    private string $url;

    public function __construct()
    {
        $config   = require ROOT . '/config/ai.php';
        $this->provider = $config['provider'];
        $provCfg  = $config[$this->provider];
        $this->apiKey = $provCfg['api_key'];
        $this->model  = $provCfg['model'];
        $this->url    = $provCfg['url'];
    }

    /**
     * Gera o texto formal de uma medida de proteção de campo.
     * Usado quando o conselheiro está em campo e precisa documentar uma medida rapidamente.
     */
    public function gerarTextoMedida(array $dados): array
    {
        $tipo       = $dados['tipo_medida'] ?? '';
        $artigo     = $dados['artigo_eca'] ?? '';
        $situacao   = $dados['situacao_relatada'] ?? '';
        $nomeCrianca= $dados['nome_crianca'] ?? 'a criança/adolescente';
        $municipio  = $dados['municipio'] ?? 'município';
        $conselheiro= $dados['conselheiro'] ?? 'Conselheiro(a) Tutelar';
        $cargo      = $dados['cargo'] ?? 'Conselheiro(a) Tutelar';
        $data       = date('d/m/Y');
        $hora       = date('H:i');

        $systemPrompt = <<<SYS
Você é um assistente jurídico especializado em direitos da criança e do adolescente.
Gera documentos formais para Conselheiros Tutelares, com linguagem técnica e fundamentação no ECA (Lei 8.069/90).
Retorne APENAS JSON puro válido — sem markdown, sem texto extra.
SYS;

        $userPrompt = <<<PROMPT
O Conselheiro Tutelar está em campo e precisa lavrar uma medida de proteção urgente.

**Dados informados:**
- Município: {$municipio}
- Nome da criança/adolescente: {$nomeCrianca}
- Tipo de medida: {$tipo}
- Artigo do ECA: {$artigo}
- Situação relatada pelo conselheiro: {$situacao}
- Conselheiro responsável: {$conselheiro}
- Data/Hora: {$data} às {$hora}

**Gere o documento no JSON abaixo (exatamente, sem campos extras):**
{
  "texto_medida": "Texto formal e completo da medida de proteção, em linguagem jurídica adequada ao ECA. Deve conter: identificação do conselheiro, data, município, descrição dos fatos, fundamentação legal (artigos do ECA), a medida determinada e prazo se aplicável. Mínimo de 3 parágrafos.",
  "fundamentacao": "Artigos do ECA e outras leis aplicadas (ex: Art. 98, Art. 101 inciso I do ECA; Art. 227 da CF/88)",
  "prazo_sugerido": "Ex: 15 dias, imediato, 30 dias",
  "orgaos_notificar": ["CRAS", "CREAS", "Delegacia", "Vara da Infância"]
}
PROMPT;

        $response = $this->callAPI($systemPrompt, $userPrompt);

        try {
            $data   = json_decode($response, true);
            // Extract content from provider wrapper
            $content = match ($this->provider) {
                'openai', 'deepseek' => $data['choices'][0]['message']['content'] ?? $response,
                'gemini'             => $data['candidates'][0]['content']['parts'][0]['text'] ?? $response,
                default              => $response,
            };
            $parsed = json_decode($content, true);
            if ($parsed && json_last_error() === JSON_ERROR_NONE) {
                return $parsed;
            }
            // Strip markdown if present
            if (preg_match('/```(?:json)?\s*([\s\S]+?)\s*```/', $content, $m)) {
                $parsed = json_decode($m[1], true);
                if ($parsed) return $parsed;
            }
        } catch (\Exception $e) {}

        // Fallback demo
        return $this->getDemoMedida($dados);
    }

    private function getDemoMedida(array $dados): array
    {
        $tipo     = $dados['tipo_medida'] ?? 'Medida de Proteção';
        $artigo   = $dados['artigo_eca'] ?? 'Art. 101 do ECA';
        $municipio= $dados['municipio'] ?? 'município';
        $cons     = $dados['conselheiro'] ?? 'Conselheiro(a) Tutelar';
        $data     = date('d/m/Y');
        $hora     = date('H:i');

        return [
            'texto_medida' => "MEDIDA DE PROTEÇÃO — {$tipo}\n\nEm {$data}, às {$hora}, no exercício das atribuições conferidas pelo Art. 136 da Lei Federal nº 8.069/1990 (Estatuto da Criança e do Adolescente), o(a) Conselheiro(a) Tutelar {$cons}, do Município de {$municipio}, determina a aplicação da presente medida de proteção.\n\nDOS FATOS: {$dados['situacao_relatada']}\n\nDA FUNDAMENTAÇÃO JURÍDICA: A situação constatada configura violação dos direitos da criança/adolescente, nos termos do Art. 98 do ECA, sendo imperativa a aplicação da medida protetiva prevista no {$artigo}, visando assegurar a proteção integral e a garantia dos direitos fundamentais.\n\nDA MEDIDA DETERMINADA: Determina-se, com fundamento no {$artigo} do ECA, a aplicação da medida de {$tipo}, devendo ser cumprida no prazo estabelecido, sob pena de comunicação ao Ministério Público para as providências cabíveis.",
            'fundamentacao'    => "{$artigo} — ECA (Lei 8.069/90); Art. 98 do ECA; Art. 227 da CF/88",
            'prazo_sugerido'   => 'Imediato',
            'orgaos_notificar' => ['CRAS', 'CREAS'],
        ];
    }

    public function analisarCaso(array $dados): array
    {
        [$systemPrompt, $userPrompt] = $this->buildPrompt($dados);
        $response = $this->callAPI($systemPrompt, $userPrompt);
        return $this->parseResponse($response);
    }

    private function getSystemPrompt(): string
    {
        return <<<SYSTEM
# Vivensi CT – Inteligência em Defesa da Criança e Adolescente

**Objetivo:** Atuar como assistente técnico para Conselheiros Tutelares na triagem, fundamentação jurídica e automação de documentos de encaminhamento.

**Fluxo Lógico de Operação:**

1. **Entrada de Dados:** Receber o relato bruto da denúncia ou demanda. Identificar atores (Vítima, Responsável, Suposto Violador) e a cronologia dos fatos.

2. **Análise de Leis e Técnica:** Cruzar os fatos exclusivamente com: ECA (Lei 8.069/90), Lei Henry Borel (Lei 14.344/22), normativas do SUAS, e leis correlatas. Identificar violações de direitos (Art. 98 do ECA). Sugerir Medidas de Proteção (Art. 101) e Medidas aplicáveis aos pais/responsáveis (Art. 129).

3. **Tomada de Decisão Assistida:** Apresentar ao Conselheiro uma lista de Encaminhamentos Sugeridos baseados na análise legal. Cada sugestão deve conter a justificativa legal detalhada para embasar o documento oficial.

4. **Automação Documental:** Gerar estrutura de dados para preenchimento de modelos (Ofícios, Requisições, Notificações). Preparar o resumo para colheita de assinatura digital via tela mobile.

**Tom de Voz:** Técnico, jurídico, imparcial e focado na proteção integral.

**Regras de Negócio:**
- Manter rastreabilidade total para fins de relatório institucional.
- Nunca extrapolar as leis citadas — fundamentar exclusivamente em ECA, Lei Henry Borel, SUAS e leis correlatas.
- Retornar sempre JSON puro e válido, sem markdown ou texto extra.
SYSTEM;
    }

    private function buildPrompt(array $d): array
    {
        $municipio    = $d['municipio'] ?? 'não informado';
        $tipoDemanda  = $d['tipo_demanda'] ?? '';
        $relato       = $d['relato_visita'] ?? '';
        $levantamento = $d['levantamento_preliminar'] ?? '';
        $redeServicos = $d['rede_servicos'] ?? '';

        $userPrompt = <<<PROMPT
Analise o caso de Conselho Tutelar abaixo e gere uma análise completa e fundamentada juridicamente.

**DADOS DO CASO:**
- Município: {$municipio}
- Tipo de Demanda: {$tipoDemanda}
- Relato da Visita: {$relato}
- Levantamento Preliminar: {$levantamento}
- Rede de Serviços Disponível: {$redeServicos}

**INSTRUÇÕES:**
Retorne um JSON válido com a seguinte estrutura exata (não inclua código markdown, apenas JSON puro):

{
  "analise_juridica": "Análise fundamentada no ECA e SUAS (artigos relevantes, contexto legal)",
  "fluxo_encaminhamento": [
    {
      "orgao": "Nome do órgão",
      "tipo": "CRAS|CREAS|UBS|CAPS|Escola|Delegacia|Vara da Infância|Ministério Público|Outro",
      "tipificacao_suas": "Tipificação conforme SUAS",
      "artigo_eca": "Art. XX do ECA",
      "urgencia": "imediata|alta|media|baixa",
      "descricao": "O que solicitar/encaminhar"
    }
  ],
  "minutas": {
    "relatorio_atendimento": "Minuta do relatório de atendimento...",
    "oficio_encaminhamento": "Minuta do ofício de encaminhamento..."
  },
  "mapa_mental_mermaid": "mindmap\n  root((Caso))\n    Demanda\n      Tipo\n    Encaminhamentos\n      Órgão 1\n      Órgão 2\n    Medidas\n      Medida 1",
  "medidas_sugeridas": [
    {
      "tipo": "Tipo da medida",
      "artigo_eca": "Art. 101 do ECA",
      "descricao": "Descrição da medida de proteção"
    }
  ],
  "prioridade": "urgente|alta|media|baixa",
  "observacoes": "Observações adicionais importantes"
}

Seja específico, técnico e fundamentado. Use linguagem jurídica adequada ao contexto do Conselho Tutelar.
PROMPT;

        return [$this->getSystemPrompt(), $userPrompt];
    }

    private function callAPI(string $systemPrompt, string $userPrompt): string
    {
        if (empty($this->apiKey)) {
            return $this->getDemoResponse();
        }

        $body = match ($this->provider) {
            'openai', 'deepseek' => json_encode([
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'temperature' => 0.3,
                'max_tokens'  => 4000,
            ]),
            'gemini' => json_encode([
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents'           => [['parts' => [['text' => $userPrompt]]]],
            ]),
        };

        $headers = match ($this->provider) {
            'openai', 'deepseek' => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->apiKey}",
            ],
            'gemini' => ['Content-Type: application/json'],
        };

        $apiUrl = $this->provider === 'gemini'
            ? $this->url . '?key=' . $this->apiKey
            : $this->url;

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 60,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return $this->getDemoResponse();
        }

        return $response;
    }

    private function parseResponse(string $raw): array
    {
        try {
            $data = json_decode($raw, true);

            // Extract content from API response format
            $content = match ($this->provider) {
                'openai', 'deepseek' => $data['choices'][0]['message']['content'] ?? $raw,
                'gemini'  => $data['candidates'][0]['content']['parts'][0]['text'] ?? $raw,
                default   => $raw,
            };

            // Try to parse as JSON
            $parsed = json_decode($content, true);
            if ($parsed && json_last_error() === JSON_ERROR_NONE) {
                return $parsed;
            }

            // Extract JSON from markdown code block if present
            if (preg_match('/```(?:json)?\s*([\s\S]+?)\s*```/', $content, $m)) {
                $parsed = json_decode($m[1], true);
                if ($parsed) return $parsed;
            }

            return ['error' => 'Resposta inválida', 'raw' => $content];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getDemoResponse(): string
    {
        return json_encode([
            'analise_juridica' => 'Com base no relato apresentado, verifica-se situação de vulnerabilidade que demanda atenção imediata do Conselho Tutelar, conforme preconizado pelo Art. 98 do ECA (Lei 8.069/90), que estabelece as medidas de proteção aplicáveis quando os direitos da criança ou adolescente forem ameaçados ou violados. A situação enquadra-se no Art. 101, incisos I ao VII, podendo ser aplicadas medidas de proteção adequadas ao caso concreto.',
            'fluxo_encaminhamento' => [
                [
                    'orgao'           => 'CRAS - Centro de Referência de Assistência Social',
                    'tipo'            => 'CRAS',
                    'tipificacao_suas'=> 'Serviço de Proteção e Atendimento Integral à Família (PAIF)',
                    'artigo_eca'      => 'Art. 101, I do ECA',
                    'urgencia'        => 'alta',
                    'descricao'       => 'Inclusão da família no PAIF para acompanhamento psicossocial e fortalecimento de vínculos familiares e comunitários.',
                ],
                [
                    'orgao'           => 'CREAS - Centro de Referência Especializado de Assistência Social',
                    'tipo'            => 'CREAS',
                    'tipificacao_suas'=> 'Serviço de Proteção Social Especial para Famílias com Indivíduos em situação de ameaça ou violação de direitos',
                    'artigo_eca'      => 'Art. 101, IV do ECA',
                    'urgencia'        => 'imediata',
                    'descricao'       => 'Atendimento especializado e inclusão no PAEFI - Serviço de Proteção e Atendimento Especializado a Famílias e Indivíduos.',
                ],
            ],
            'minutas' => [
                'relatorio_atendimento' => "RELATÓRIO DE ATENDIMENTO DO CONSELHO TUTELAR\n\nProcesso nº: [PROTOCOLO]\nData: " . date('d/m/Y') . "\n\nI - DA IDENTIFICAÇÃO\nNome da Criança/Adolescente: [NOME]\nData de Nascimento: [DATA]\n\nII - DOS FATOS\nEm visita domiciliar realizada nesta data, foram constatados os seguintes fatos: [RELATO]\n\nIII - DA FUNDAMENTAÇÃO JURÍDICA\nA situação relatada configura violação dos direitos previstos no Art. 227 da Constituição Federal e Art. 4º do ECA, sendo aplicável o disposto no Art. 98 e seguintes do referido Estatuto.\n\nIV - DAS PROVIDÊNCIAS\nDiante do exposto, este Conselho Tutelar determina as seguintes medidas de proteção:\n[MEDIDAS]\n\nV - DA CONCLUSÃO\nO presente relatório é elaborado para todos os fins de direito.\n\n[LOCAL], " . date('d de F de Y') . "\n\n_________________________\nConselheiro(a) Tutelar",
                'oficio_encaminhamento' => "OFÍCIO Nº [NÚMERO]/[ANO]\n\nAo Excelentíssimo Senhor\nDiretor do [ORGÃO]\n\nAssunto: Encaminhamento - Caso de Proteção Integral\n\nSenhor Diretor,\n\nO Conselho Tutelar do Município de [MUNICIPIO], no exercício das atribuições que lhe são conferidas pelo Art. 131 e seguintes da Lei Federal nº 8.069/90 (ECA), vem respeitosamente encaminhar a criança/adolescente [NOME], para atendimento especializado nessa instituição.\n\nA situação identificada demanda intervenção imediata, considerando os seguintes aspectos: [DESCRICAO]\n\nSolicitamos que os resultados do atendimento sejam comunicados a este Conselho Tutelar, para acompanhamento do caso.\n\nAtenciosamente,\n\n_________________________\nConselheiro(a) Tutelar\nRegistro nº [REGISTRO]",
            ],
            'mapa_mental_mermaid' => "mindmap\n  root((Caso CT))\n    Demanda Identificada\n      Vulnerabilidade Familiar\n      Risco Psicossocial\n    Encaminhamentos\n      CRAS\n        PAIF\n        Benefícios Sociais\n      CREAS\n        PAEFI\n        Atendimento Especializado\n    Medidas ECA\n      Art 101 - Proteção\n      Inclusão em Programa\n    Monitoramento\n      Visita de Retorno\n      Relatório em 30 dias",
            'medidas_sugeridas' => [
                [
                    'tipo'       => 'Inclusão em programa oficial ou comunitário de proteção à família',
                    'artigo_eca' => 'Art. 101, IV do ECA',
                    'descricao'  => 'Encaminhamento da família para serviços da rede socioassistencial (PAIF/PAEFI).',
                ],
                [
                    'tipo'       => 'Orientação, apoio e acompanhamento temporários',
                    'artigo_eca' => 'Art. 101, II do ECA',
                    'descricao'  => 'Acompanhamento pela equipe técnica do CRAS/CREAS durante período de 90 dias.',
                ],
            ],
            'prioridade' => 'alta',
            'observacoes' => 'MODO DEMONSTRAÇÃO - Configure uma chave de API (OpenAI, Gemini ou DeepSeek) no arquivo config/ai.php para análises reais personalizadas. Esta resposta é um modelo demonstrativo baseado no ECA e SUAS.',
        ]);
    }
}
