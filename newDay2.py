import mysql.connector

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'isp_termproject',
}

conn = mysql.connector.connect(**db_config)
cursor = conn.cursor()

source_table = 'trendingyesterday'
destination_table = 'trending2daysago'

clear_table_query = f"DELETE FROM {destination_table}"
cursor.execute(clear_table_query)

conn.commit()

move_data_query = f"INSERT INTO {destination_table} SELECT * FROM {source_table}"
cursor.execute(move_data_query)

conn.commit()
conn.close()

print(f"Data moved from {source_table} to {destination_table}.")
