<?php
class WhatsApiService {
  private string $base;
  public function __construct(string $base) { $this->base = rtrim($base, '/'); }

  public function status(): array {
    return $this->request('GET', '/status-json');
  }

  public function send(string $to, string $message): array {
    return $this->request('POST', '/send', ['to' => $to, 'message' => $message]);
  }

  private function request(string $method, string $path, ?array $payload = null): array {
    $url = $this->base . $path;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($method === 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    }
    $res = curl_exec($ch);
    if ($res === false) { throw new Exception(curl_error($ch)); }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($res, true);
    if ($code >= 400) { throw new Exception(($data['error'] ?? 'HTTP '.$code)); }
    return $data ?: [];
  }
}
