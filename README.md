
```bash
composer install
```

- Créer à la racine du projet un fichier `.env.local`
- Copier la ligne de code ci-dessous dans votre fichier `.env.local`

```bash
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```

- Ouvrir un terminal et taper ces lignes de commandes une par une :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

Après ces lignes de commandes ci-dessus, vous aurez une base de données avec de fausses données prêtes à l'emploi.

- Créer les clées publiques et privées nécessaires à la gestion du JWT (Json Web Token) avec cette ligne de commande (l'option --skip-if-exists permet de ne pas générer de clées si elles le sont déjà) :

```bash
php bin/console lexik:jwt:generate-keypair --skip-if-exists
```

- Pour les tests, il faut générer d'autres clés ainsi (mettre la passphrase à `pass`):
  
```bash
openssl genrsa -out config/jwt/private-test.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem
```

- Il vous suffit désormais de lancer votre serveur php :

```bash
php -S localhost:8080 -t public/
```

- Enjoy !


POUR TOKEN :


mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
Try this.
