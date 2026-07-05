ALTER TABLE tb_usuarios ADD COLUMN google2fa_secret VARCHAR(255) DEFAULT NULL AFTER token_expira;
