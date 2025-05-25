# Parar de Fumar - Aplicativo Web

Um aplicativo web mobile-first para ajudar pessoas a pararem de fumar, oferecendo apoio diário, metas claras e motivação contínua.

## Características

- Design mobile-first com aparência de app nativo
- Dashboard com progresso diário
- Sistema de metas (curto, médio e longo prazo)
- Registro de recaídas
- Mensagens motivacionais diárias
- Cálculo de economia
- Modo claro/escuro
- Perfil personalizável

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Navegador web moderno

## Instalação

1. Clone este repositório para seu servidor web:
```bash
git clone https://github.com/seu-usuario/parar-de-fumar.git
```

2. Importe o banco de dados:
```bash
mysql -u seu_usuario -p < database.sql
```

3. Configure a conexão com o banco de dados:
   - Abra o arquivo `php/conexao.php`
   - Atualize as constantes DB_HOST, DB_USER, DB_PASS e DB_NAME com suas credenciais

4. Certifique-se que o servidor web tem permissão de escrita nas pastas:
   - `/assets`
   - `/php`

## Estrutura de Arquivos

```
/
├── assets/         # Imagens e recursos estáticos
├── css/           # Arquivos CSS
├── js/            # Arquivos JavaScript
├── php/           # Arquivos PHP
├── index.php      # Página inicial
├── cadastro.php   # Página de cadastro
├── login.php      # Página de login
├── dashboard.php  # Dashboard principal
├── metas.php      # Página de metas
├── perfil.php     # Página de perfil
└── README.md      # Este arquivo
```

## Uso

1. Acesse o aplicativo através do navegador
2. Crie uma conta ou faça login
3. Configure seus dados iniciais (cigarros por dia, preço da carteira, etc.)
4. Acompanhe seu progresso no dashboard
5. Registre recaídas quando necessário
6. Acompanhe suas metas na página de metas
7. Personalize seu perfil quando desejar

## Segurança

- Todas as senhas são armazenadas com hash seguro
- Proteção contra SQL Injection usando prepared statements
- Validação de dados em todos os formulários
- Proteção contra XSS usando htmlspecialchars
- Sessões seguras

## Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Crie um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para detalhes. 