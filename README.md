Nupres WebApp Api
========================

Comando sql:

mysqldump -u root -p --add-drop-database --add-drop-table --comments --complete-insert --create-options --extended-insert --force  --quote-names --routines --single-transaction --triggers --verbose nupres_dev_demo01 > /var/www/html/nupres-webapp-backend-symfonyfwk/src/Nupres/Bundle/ApiBundle/Resources/sql/db.sql

Removiendo el definer:

mysqldump -u root -p --add-drop-database --add-drop-table --comments --complete-insert --create-options --extended-insert --force --quote-names --routines --single-transaction --triggers --verbose nupres_dev_demo01 | sed -e 's/DEFINER[ ]*=[ ]*[^*]*\*/\*/' > /home/diecam/Desktop/db2.sql


Importar:

mysql -u nupre_dev_demo01 -p nupres_dev_demo01 < /home/diecam/Desktop/db.sql

Note-2: Use -R and --triggers to keep the routines and triggers of original database. They are not copied by default.

Mysql Options: https://dev.mysql.com/doc/refman/5.7/en/mysql-command-options.html
