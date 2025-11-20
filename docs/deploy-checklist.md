# Checklist de Deploy em Produção

- [ ] HTTPS habilitado no domínio
- [ ] `config.php` apontando para o banco de produção
- [ ] `display_errors` desativado em produção
- [ ] Pasta `uploads/` com permissão de escrita
- [ ] Backup diário do servidor habilitado no provedor
- [ ] Workflow de backup do GitHub Actions funcionando
- [ ] Usuário ADMIN criado e senha trocada
- [ ] Usuário OPERADOR criado para o dia a dia
