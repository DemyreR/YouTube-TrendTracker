import json
import requests
from googleapiclient.discovery import build
from datetime import datetime, timedelta
import pandas as pd
import mysql.connector

API_KEY = 'Enter Your Key Here'
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'isp_termproject',
}

conn = mysql.connector.connect(**db_config)
cursor = conn.cursor()

table_name = 'trendingcurrent'

clear_table_query = f"DELETE FROM {table_name}"
cursor.execute(clear_table_query)

conn.commit()