<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Sistema de Agendamento – Clínica

Aplicação Laravel com autenticação (admin/cliente), controle de agendamentos e integrações planejadas (Asaas, Google Maps/Geocoding e WhatsApp).
O registro de usuários é fechado (feito apenas pelo admin).
O login redireciona automaticamente para o dashboard do Admin ou do Cliente conforme o role.

1) Requisitos

PHP 8.2+ com extensões: openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, curl, fileinfo

Composer 2+

MySQL/MariaDB (ou outro banco suportado)

Node 18+ e npm (para assets com Vite)

Git (opcional, para clonar o repositório)

Dica (Windows): use Xampp ou Laragon para PHP + MySQL prontos.

2) Baixar o projeto
# via git
git clone https://github.com/SEU_USUARIO/SEU_REPO.git clinica
cd clinica


Ou copie os arquivos manualmente para uma pasta, ex: F:\clinica\clinica.

3) Instalar dependências PHP
composer install

4) Configurar o .env

Crie seu .env a partir do exemplo:

cp .env.example .env


Edite as variáveis de banco (exemplo MySQL local):

APP_NAME="Clinica"
APP_ENV=local
APP_KEY=    # será gerada no passo 5
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinica
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database


Já deixamos Schema::defaultStringLength(191) no AppServiceProvider, o que evita o erro “Specified key was too long” em MySQL.

Crie o banco vazio no MySQL:

CREATE DATABASE clinica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

5) Gerar APP_KEY
php artisan key:generate

6) Migrar e popular tabelas

Execute as migrations:

php artisan migrate


Popule especialidades (com preços):

php artisan db:seed --class=Database\\Seeders\\SpecialtiesSeeder


Se existir o AdminSeeder no projeto, também execute:

php artisan db:seed --class=Database\\Seeders\\AdminSeeder


Caso não tenha o seeder de admin, crie um admin via SQL/Tinker:

SQL:

INSERT INTO users (name,email,password,role,created_at,updated_at)
VALUES ('Admin', 'admin@clinica.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: password
'admin', NOW(), NOW());


ou Tinker:

php artisan tinker
>>> \App\Models\User::create([
... 'name'=>'Admin',
... 'email'=>'admin@clinica.com',
... 'password'=>bcrypt('password'),
... 'role'=>'admin'
... ]);

7) Rodar o servidor
php artisan serve


Acesse: http://127.0.0.1:8000

8) Rodar o front (Vite)

Durante o desenvolvimento:

npm install
npm run dev


Em produção:

npm run build

9) Login e Fluxo

Login: /login

O sistema redireciona após login:

role = admin → /admin/dashboard

role = client → /client/dashboard

Usuários de exemplo

Admin: admin@clinica.com / password

Cliente (se criado via SQL): cliente@teste.com / password

Registro está desabilitado para público. Somente o Admin cadastra clientes no painel.

10) Rotas principais

Cliente

/client/dashboard – Home do cliente

/appointments/create – Solicitar consulta

Formulário com Especialidade (lista do BD com preço padrão R$ 30; Psicólogo online e Nutricionista online = R$ 40)

CEP com auto–preenchimento de Cidade/UF via ViaCEP

Cidade e Estado (UF) obrigatórios

Campo “Deseja indicar uma clínica ou médico?” (opcional)

Admin

/admin/dashboard – Home do admin

(CRUDs do admin serão adicionados nas próximas entregas)

11) Estrutura técnica (resumo)

Middleware de função/role: App\Http\Middleware\RoleMiddleware

Alias em Kernel.php:

'role' => \App\Http\Middleware\RoleMiddleware::class,


Redirecionamento pós-login: routes/web.php rota /dashboard

Especialidades e Preços:

Migration: create_specialties_table

Seeder: SpecialtiesSeeder (R$ 30 padrão; Nutricionista/Psicólogo online = R$ 40)

Model: App\Models\Specialty

Tela usa valor dinâmico no select, exibindo Coparticipação ao escolher a especialidade

12) Comandos úteis

Limpar caches:

php artisan optimize:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload -o


Listar rotas:

php artisan route:list --no-ansi

13) Problemas comuns (FAQ)

“Specified key was too long; max key length 1000 bytes”
→ Já mitigado com Schema::defaultStringLength(191) no AppServiceProvider. Se aparecer, confirme que o arquivo está em produção e limpe caches (php artisan optimize:clear).

“Target class [role] does not exist.”
→ Verifique:

app/Http/Middleware/RoleMiddleware.php existe?

Em app/Http/Kernel.php, consta:

'role' => \App\Http\Middleware\RoleMiddleware::class,


Limpe caches/autoload:

composer dump-autoload -o
php artisan optimize:clear


Duplicação test@example.com no seeder
→ Se ainda existir o seeder padrão do Breeze gerando test@example.com, remova/edite ou execute migrate:fresh --seed apenas com os seeders desejados.

Erro de conexão com o banco
→ Confirme credenciais do .env e se o DB existe:

DB_DATABASE=clinica
DB_USERNAME=root
DB_PASSWORD=


Vite (front) não atualiza
→ Rode npm run dev em uma janela separada do terminal. Se necessário, ctrl+c e inicie novamente.

14) Próximos passos (roadmap)

CRUD Admin: Clínicas, Especialidades (nome/preço), Clientes, Agendamentos.

Appointments: migration/model/controller para persistir solicitações (status: requested, awaiting_payment, paid, confirmed, completed, canceled).

Integrações:

Asaas (cobrança PIX/cartão + webhook)

Google Maps / Geocoding (geolocalizar clínicas e buscar por raio)

WhatsApp (mensagem automática na confirmação de pagamento/agendamento)

15) Licença

Projeto privado. Uso interno do time.