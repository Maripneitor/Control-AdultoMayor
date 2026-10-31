#!/bin/sh
set -e

echo "============================================================"
echo "   Iniciando VitalSenior - Ecosistema Clínico Django MVT"
echo "============================================================"

echo "Verificando disponibilidad de la base de datos PostgreSQL..."
python - << 'EOF'
import time, os, sys
try:
    import psycopg
except ImportError:
    import psycopg2 as psycopg

host = os.environ.get('DB_HOST', 'db')
port = int(os.environ.get('DB_PORT', 5432))
user = os.environ.get('DB_USER', 'postgres')
password = os.environ.get('DB_PASS', 'postgres')
dbname = os.environ.get('DB_NAME', 'vitalsenior')

max_retries = 30
for i in range(max_retries):
    try:
        conn = psycopg.connect(host=host, port=port, user=user, password=password, dbname=dbname, connect_timeout=3)
        conn.close()
        print(f"-> Base de datos PostgreSQL '{dbname}' lista en {host}:{port}.")
        sys.exit(0)
    except Exception as e:
        print(f"Esperando a PostgreSQL ({i+1}/{max_retries})...")
        time.sleep(1)

print("Error: No se pudo conectar a PostgreSQL.")
sys.exit(1)
EOF

echo "Aplicando migraciones de base de datos (--fake-initial)..."
python manage.py migrate --fake-initial --noinput

echo "Verificando consistencia del sistema..."
python manage.py check

echo "Generando esquema de tomas diarias de medicamentos para hoy..."
python manage.py cron_medicamentos || true

echo "Servidor listo. Arrancando aplicación VitalSenior..."
exec "$@"
