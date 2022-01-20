## DocuSing - Generador de Token

Implementación rapida de generación de token para [DocuSign](https://developers.docusign.com/platform/auth/jwt/jwt-get-token/) JWT Grant authentication.

Para utilizar con [SDK de Android](https://github.com/docusign/mobile-android-sdk)

### Ejemplo de respuesta

```
{
    "access_token": "eyJ0eXAiOi...",
    "expires_in": "3600",
    "refresh_token": null,
    "scope": "signature impersonation",
    "token_type": "Bearer",
    "integration_key": "abc..."
}
```
