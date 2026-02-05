# Test SmartClick360 API
# PowerShell Script

Write-Host "`n=== TESTE API SMARTCLICK360 ===`n" -ForegroundColor Cyan

# 1. LOGIN
Write-Host "1. Fazendo login..." -ForegroundColor Yellow
$loginBody = @{
    email = "admin@demo.com"
    password = "password"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod `
        -Uri "https://demo.smartclick360.test/api/v1/auth/login" `
        -Method Post `
        -Body $loginBody `
        -ContentType "application/json" `
        -SkipCertificateCheck

    $token = $loginResponse.data.token
    Write-Host "   ✓ Login realizado com sucesso!" -ForegroundColor Green
    Write-Host "   Token: $($token.Substring(0,50))...`n" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Erro no login: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 2. GET /admin/auth/me
Write-Host "2. Consultando dados do usuário autenticado..." -ForegroundColor Yellow
$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

try {
    $meResponse = Invoke-RestMethod `
        -Uri "https://demo.smartclick360.test/api/v1/admin/auth/me" `
        -Method Get `
        -Headers $headers `
        -SkipCertificateCheck

    Write-Host "   ✓ Usuário: $($meResponse.data.user.email)" -ForegroundColor Green
    Write-Host "   Nome: $($meResponse.data.user.person.name)`n" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Erro ao consultar usuário: $($_.Exception.Message)" -ForegroundColor Red
}

# 3. GET /admin/persons
Write-Host "3. Listando persons..." -ForegroundColor Yellow
try {
    $personsResponse = Invoke-RestMethod `
        -Uri "https://demo.smartclick360.test/api/v1/admin/persons" `
        -Method Get `
        -Headers $headers `
        -SkipCertificateCheck

    Write-Host "   ✓ Total de registros: $($personsResponse.meta.total)" -ForegroundColor Green
    Write-Host "`n   Registros:" -ForegroundColor Gray
    $personsResponse.data | ForEach-Object {
        Write-Host "   - ID: $($_.id) | Nome: $($_.name) | Email: $($_.email)" -ForegroundColor White
    }
    Write-Host ""
} catch {
    Write-Host "   ✗ Erro ao listar persons: $($_.Exception.Message)" -ForegroundColor Red
}

# 4. POST /admin/persons
Write-Host "4. Criando nova person..." -ForegroundColor Yellow
$newPersonBody = @{
    name = "Teste PowerShell"
    type = "individual"
    document = "98765432100"
    email = "teste.ps@example.com"
    phone = "11987654321"
    is_customer = $true
} | ConvertTo-Json

try {
    $createResponse = Invoke-RestMethod `
        -Uri "https://demo.smartclick360.test/api/v1/admin/persons" `
        -Method Post `
        -Body $newPersonBody `
        -Headers $headers `
        -ContentType "application/json" `
        -SkipCertificateCheck

    $newId = $createResponse.data.id
    Write-Host "   ✓ Person criada com sucesso! ID: $newId`n" -ForegroundColor Green

    # 5. GET /admin/persons/{id}
    Write-Host "5. Consultando person criada (ID: $newId)..." -ForegroundColor Yellow
    $personResponse = Invoke-RestMethod `
        -Uri "https://demo.smartclick360.test/api/v1/admin/persons/$newId" `
        -Method Get `
        -Headers $headers `
        -SkipCertificateCheck

    Write-Host "   ✓ Nome: $($personResponse.data.name)" -ForegroundColor Green
    Write-Host "   Email: $($personResponse.data.email)" -ForegroundColor Gray
    Write-Host "   Documento: $($personResponse.data.document)`n" -ForegroundColor Gray
} catch {
    Write-Host "   ✗ Erro: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "=== TESTES CONCLUÍDOS ===`n" -ForegroundColor Cyan
