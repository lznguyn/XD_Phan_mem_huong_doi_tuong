class AppConfig {
  static const env = String.fromEnvironment('ENV', defaultValue: 'DEV');
  static const apiBase = String.fromEnvironment(
    'API_BASE',
    defaultValue: 'https://dev.api.example.com',
  );
}
