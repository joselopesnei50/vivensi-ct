<?php
return [
    // Configure sua chave API aqui (OpenAI, Gemini ou DeepSeek)
    'provider' => 'openai', // openai | gemini | deepseek
    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),
        'model'   => 'gpt-4o',
        'url'     => 'https://api.openai.com/v1/chat/completions',
    ],
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model'   => 'gemini-1.5-flash',
        'url'     => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
    ],
    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY', ''),
        'model'   => 'deepseek-chat',
        'url'     => 'https://api.deepseek.com/v1/chat/completions',
    ],
];
