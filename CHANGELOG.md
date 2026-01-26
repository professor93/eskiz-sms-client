# Changelog

All notable changes to `eskiz-sms-client` will be documented in this file.

## 1.1.0 - 2026-01-26

### Added
- **Exception classes** for better error handling:
  - `EskizException` - Base exception for all API errors
  - `AuthenticationException` - For authentication failures
  - `RateLimitException` - For rate limit errors (429)
- **Response DTOs** for type-safe responses:
  - `SendResponse` - SMS send result with `id`, `status`, `message`, `phone`
  - `UserResponse` - User info with `id`, `name`, `email`, `role`, `status`, `balance`, `isVip`
  - `LimitResponse` - Limits info with `limit`, `used`, `remaining`, `resetAt`
- **Auto token refresh** - Automatically refreshes token on 401 responses
- **New methods**:
  - `user()` - Get user information (returns `UserResponse`)
  - `balance()` - Get account balance
  - `refreshToken()` - Manually refresh authentication token
- **Comprehensive test suite** with unit and feature tests

### Changed
- **BREAKING**: Dropped support for Laravel 9.x and 10.x
- **BREAKING**: Now requires PHP 8.3+
- **BREAKING**: `send()` method now returns `SendResponse` instead of `array`
- **BREAKING**: `limits()` method now returns `LimitResponse` instead of `array`
- Added support for Laravel 11.x and 12.x
- Updated all dependencies to their latest 2026 versions
- Updated GitHub Actions workflows to use latest action versions
- Updated test matrix to cover PHP 8.3 and 8.4

### Deprecated
- `about()` method - Use `user()` instead. Will be removed in v2.0.

### Note
If you need Laravel 9.x or 10.x support, please use version 1.0.x.

## 1.0.7 - 2022-10-07

Added sender (from) to config.

## 1.0.6 - 2022-08-30

Fix config checker function

## 1.0.5 - 2022-08-30

Add Proxy config functionality

## 1.0.2 - 2022-06-22

Fix sender

## 1.0.1 - 2022-06-22

Fix config name in ServiceProvider

## 1.0.0 - 2022-06-22

First release
