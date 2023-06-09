#!/usr/bin/env python
# coding: utf-8

# In[ ]:


import nltk
nltk.download()
import numpy as np
import pandas as pd
nltk.download('wordnet')
nltk.download('punket')
nltk.download('stopwords')
nltk.download('omw-1.4')
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize
from nltk.stem.wordnet import WordNetLemmatizer
from ast import literal_eval


# In[ ]:


#UPLOAD_DATASET
data = pd.read_csv("D:/Datasets/New folder (3)/Hotel_Reviews.csv")


# In[ ]:


data.head()


# In[ ]:


data.tail()


# In[ ]:


data.shape


# In[ ]:


data.info()


# In[ ]:


print(data.Hotel_Address.head())


# In[ ]:


data.Hotel_Address = data.Hotel_Address.str.replace("United Kingdom", "UK")
data.Hotel_Address = data.Hotel_Address.str.replace("Netherlands", "NL")
data.Hotel_Address = data.Hotel_Address.str.replace("France", "FR")
data.Hotel_Address = data.Hotel_Address.str.replace("Spain", "ES")
data.Hotel_Address = data.Hotel_Address.str.replace("Italy", "IT")
data.Hotel_Address = data.Hotel_Address.str.replace("Austria", "AT")


# In[ ]:


# Now I will split the address and pick the last word in the address to identify the country
data["countries"] = data.Hotel_Address.apply(lambda x: x.split(' ')[-1])


# In[ ]:


#check the country column
print(data.countries.unique())


# In[ ]:


#choose the column for reccommendation
data.columns


# In[ ]:


#drop the unnecessary columns

data.drop(['Additional_Number_of_Scoring',
       'Review_Date','Reviewer_Nationality',
       'Negative_Review', 'Review_Total_Negative_Word_Counts',
       'Total_Number_of_Reviews', 'Positive_Review',
       'Review_Total_Positive_Word_Counts',
       'Total_Number_of_Reviews_Reviewer_Has_Given', 'Reviewer_Score',
       'days_since_review', 'lat', 'lng'],1,inplace=True)


# In[ ]:


data.head()


# In[ ]:


#create a function to convert the strings of list into a normal list and then apply it to the “Tags” column in the dataset

def impute(column):
    column = column[0]
    if (type(column) != list):
        return "".join(literal_eval(column))
    else:
        return column


# In[ ]:


data.head()


# In[ ]:


#lowercase the “Tags” and “countries” column for simplicity

data['countries'] = data['countries'].str.lower()
data['Tags'] = data['Tags'].str.lower()


# In[ ]:


def recommend_hotel(location, description):
    description = description.lower()
    word_tokenize(description)
    stop_words = stopwords.words('english')
    lemm = WordNetLemmatizer()
    filtered  = {word for word in description if not word in stop_words}
    filtered_set = set()
    for fs in filtered:
        filtered_set.add(lemm.lemmatize(fs))

    country = data[data['countries']==location.lower()]
    country = country.set_index(np.arange(country.shape[0]))
    list1 = []; list2 = []; cos = [];
    for i in range(country.shape[0]):
        temp_token = word_tokenize(country["Tags"][i])
        temp_set = [word for word in temp_token if not word in stop_words]
        temp2_set = set()
        for s in temp_set:
            temp2_set.add(lemm.lemmatize(s))
        vector = temp2_set.intersection(filtered_set)
        cos.append(len(vector))
    country['similarity']=cos
    country = country.sort_values(by='similarity', ascending=False)
    country.drop_duplicates(subset='Hotel_Name', keep='first', inplace=True)
    country.sort_values('Average_Score', ascending=False, inplace=True)
    country.reset_index(inplace=True)
    return country[["Hotel_Name", "Average_Score", "Hotel_Address"]].head()


# In[ ]:





# In[ ]:





# In[ ]:





# In[ ]:





# In[ ]:




