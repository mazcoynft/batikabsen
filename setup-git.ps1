# 🔧 Git Setup Script untuk USSIBATIK Absen
# Script untuk setup Git configuration dengan mudah

param(
    [string]$Name = "",
    [string]$Email = ""
)

$Green = "Green"
$Blue = "Blue"
$Yellow = "Yellow"
$Red = "Red"
$Cyan = "Cyan"

Write-Host "🔧 Git Configuration Setup" -ForegroundColor $Green
Write-Host "==========================" -ForegroundColor $Green

# Check if Git is installed
try {
    git --version | Out-Null
    Write-Host "✅ Git is installed" -ForegroundColor $Green
} catch {
    Write-Host "❌ Git is not installed!" -ForegroundColor $Red
    Write-Host "Please install Git from: https://git-scm.com/download/win" -ForegroundColor $Yellow
    exit 1
}

# Get user input if not provided
if (-not $Name) {
    $Name = Read-Host "Enter your name (e.g., 'John Doe')"
}

if (-not $Email) {
    $Email = Read-Host "Enter your email (e.g., 'john@example.com')"
}

# Validate inputs
if (-not $Name -or -not $Email) {
    Write-Host "❌ Name and email are required!" -ForegroundColor $Red
    exit 1
}

Write-Host ""
Write-Host "🔧 Setting up Git configuration..." -ForegroundColor $Blue

# Set global Git configuration
try {
    git config --global user.name $Name
    git config --global user.email $Email
    
    Write-Host "✅ Git configuration set successfully!" -ForegroundColor $Green
    Write-Host "   Name: $Name" -ForegroundColor $Cyan
    Write-Host "   Email: $Email" -ForegroundColor $Cyan
    
} catch {
    Write-Host "❌ Failed to set Git configuration!" -ForegroundColor $Red
    exit 1
}

# Verify configuration
Write-Host ""
Write-Host "🔍 Verifying configuration..." -ForegroundColor $Blue

$configName = git config --global user.name
$configEmail = git config --global user.email

Write-Host "✅ Current Git configuration:" -ForegroundColor $Green
Write-Host "   Name: $configName" -ForegroundColor $Cyan
Write-Host "   Email: $configEmail" -ForegroundColor $Cyan

# Set up some useful Git defaults
Write-Host ""
Write-Host "⚙️  Setting up useful Git defaults..." -ForegroundColor $Blue

git config --global init.defaultBranch main
git config --global core.autocrlf true  # For Windows
git config --global pull.rebase false   # Merge strategy
git config --global core.editor "code --wait"  # VS Code as editor (if available)

Write-Host "✅ Git defaults configured!" -ForegroundColor $Green

# Show next steps
Write-Host ""
Write-Host "🎉 Git Setup Complete!" -ForegroundColor $Green
Write-Host "======================" -ForegroundColor $Green
Write-Host ""
Write-Host "📋 Now you can run your commit:" -ForegroundColor $Blue
Write-Host '   git add .' -ForegroundColor $Cyan
Write-Host '   git commit -m "first mazcoy commit"' -ForegroundColor $Cyan
Write-Host '   git push origin main' -ForegroundColor $Cyan
Write-Host ""
Write-Host "🚀 Or use our automated deployment:" -ForegroundColor $Blue
Write-Host '   .\deploy-all.ps1 -CommitMessage "first mazcoy commit"' -ForegroundColor $Cyan

Write-Host ""
Write-Host "✨ Git is ready to use!" -ForegroundColor $Green