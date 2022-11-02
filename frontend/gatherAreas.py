import json

# gatherAreas.py
# -- Run file to get formatted dictionaries and
#    statistics
# -- Function will return list of formatted dicts    

# Get raw dictionary
#jdict = get_jdict()

#jdict has to be recieved as a response from rabbitmq
#It will also be cleaned up before it gets here


# Get area dictionary
area_dicts = []
raw_areas = jdict['data']['areas'][0]['children']
for raw_area in raw_areas:
  for child in raw_area['children']:
    # print(str(child['children']) + '\n')
    for area_dict in child['children']:
      area_dicts.append(area_dict)

# Assign climb count and gather total statistics
area_count, total_climb_count = 0, 0
f_area_dicts = []

for area_dict in area_dicts:
    area_count += 1

    # Print non-formatted area dictionaries
    # - print(area_dict['areaName'])
    # - print(area_dict)

    area_climb_count = 0
    for climbs in area_dict['climbs']:
        area_climb_count += 1
    
    # Old method for reference
    # - total_climb_count += area_climb_count
    # - f_area_dict = area_dict
    # - f_area_dict['climbs'] = area_climb_count
    # - f_area_dicts.append(f_area_dict)

    f_area_dict = {'areaName': area_dict['areaName'],
                     'climbs': area_climb_count,
                     'lat': area_dict['metadata']['lat'],
                     'lng': area_dict['metadata']['lng']}
    f_area_dicts.append(f_area_dict)
    

# Print formatted area dictionaries
# - for f_area_dict in f_area_dicts:
# -     print(f_area_dict)
    
print(f'----------\nStatistics:\n\nArea count: {area_count}')
print(f'Total climb count: {total_climb_count}')

def get_raw_data():
    return f_area_dicts
