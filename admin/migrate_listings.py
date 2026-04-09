import mysql.connector
import bcrypt
import sys

# Connection details
config = {
    'user': 'd04537d6',
    'password': '01528797Mb##',
    'host': 'localhost',
    'database': 'd04537d6',
}

def migrate():
    try:
        print("Connecting to database...")
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor(dictionary=True)

        # 1. Check if user already exists
        cursor.execute("SELECT id FROM users WHERE username = 'Emlaxia'")
        user = cursor.fetchone()

        if user:
            user_id = user['id']
            print(f"User 'Emlaxia' already exists with ID: {user_id}")
        else:
            # Create new user
            password = b"Ibocan19905757"
            # Note: bcrypt.hashpw returns a hash that is compatible with PHP's password_hash(PASSWORD_BCRYPT)
            hashed = bcrypt.hashpw(password, bcrypt.gensalt(rounds=10))
            hashed_str = hashed.decode('utf-8')
            
            # The PHP users table fields: username, email, password, phone, full_name, user_type, status, email_verified
            sql = """
            INSERT INTO users (username, email, password, full_name, user_type, status, email_verified) 
            VALUES (%s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(sql, ('Emlaxia', 'info@emlaxia.com', hashed_str, 'Emlaxia', 'emlakci', 'active', 1))
            user_id = cursor.lastrowid
            print(f"Created user 'Emlaxia' with ID: {user_id}")

        # 2. Migrate ownerless listings
        cursor.execute("UPDATE listings SET user_id = %s, user_type = 'emlakci' WHERE user_id IS NULL", (user_id,))
        affected = cursor.rowcount
        print(f"Successfully migrated {affected} listings to 'Emlaxia'.")

        conn.commit()
        cursor.close()
        conn.close()
        print("SUCCESS: Migration complete.")

    except Exception as e:
        print(f"CRITICAL ERROR: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    migrate()
