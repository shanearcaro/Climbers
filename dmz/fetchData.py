import requests as r
import json

reqdict = {"query MyQuery":
            {"areas":
                ['area_name','uuid']
            }
          }

{"query":"query Example1 {\n  areas {\n    area_name\n    uuid\n  }\n}\n\nquery Ex2 {\n  area(uuid: \"c441f90b-7951-5ff1-b10a-e559fe24d2bc\") {\n    id\n    totalClimbs\n  }\n}\n","variables":null,"operationName":"Ex2"}

json_dict = json.dumps(reqdict)

myreq = r.post('https://stg-api.openbeta.io', 
                headers={'content-type': 'application/json'},
                json=json_dict,
            )

print(json_dict)
print("\n------------------------------\n")
print(myreq.text)