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

youtube = build('youtube', 'v3', developerKey=API_KEY)

def get_date():
    date = datetime.now().date()
    return date

type_id_to_name = {
    '1': 'Film & Animation',
    '2': 'Autos & Vehicles',
    '10': 'Music',
    '15': 'Pets & Animals',
    '17': 'Sports',
    '18': 'Short Movies',
    '19': 'Travel & Events',
    '20': 'Gaming',
    '21': 'Videoblogging',
    '22': 'People & Blogs',
    '23': 'Comedy',
    '24': 'Entertainment',
    '25': 'News & Politics',
    '26': 'Howto & Style',
    '27': 'Education',
    '28': 'Science & Technology',
    '29': 'Nonprofits & Activism',
    '30': 'Movies',
    '31': 'Anime/Animation',
    '32': 'Action/Adventure',
    '33': 'Classics',
    '34': 'Comedy',
    '35': 'Documentary',
    '36': 'Drama',
    '37': 'Family',
    '38': 'Foreign',
    '39': 'Horror',
    '40': 'Sci-Fi/Fantasy',
    '41': 'Thriller',
    '42': 'Shorts',
    '43': 'Shows',
    '44': 'Trailers',
}

video_data = []

next_page_token = None
while True:
    response = youtube.videos().list(
        part='snippet,statistics',
        chart='mostPopular',
        regionCode='US',
        maxResults=250,
        pageToken=next_page_token
    ).execute()

    date = get_date()

    for video in response['items']:
        title = video['snippet']['title']
        video_id = video['id']
        view_count = video['statistics']['viewCount']
        like_count = video['statistics'].get('likeCount', 0)
        video_type_id = video['snippet']['categoryId']
        tags = ', '.join(video['snippet'].get('tags', []))[:255]

        video_type = type_id_to_name.get(video_type_id, 'Unknown')
        video_data.append({
            'Title': title,
            'Video ID': video_id,
            'View Count': view_count,
            'Like Count': like_count,
            'Tags': tags,
            'Video Type': video_type,
            'Trending Date': date
        })

    next_page_token = response.get('nextPageToken')
    if not next_page_token:
        break

df = pd.DataFrame(video_data)

for index, row in df.iterrows():
    sql = "INSERT INTO trendingcurrent (video_id, title, view_count, like_count, video_type, tags, date) VALUES (%s, %s, %s, %s, %s, %s, %s)"
    values = (row['Video ID'], row['Title'], row['View Count'], row['Like Count'], row['Video Type'], row['Tags'], row['Trending Date'])
    cursor.execute(sql, values)

conn.commit()
conn.close()
