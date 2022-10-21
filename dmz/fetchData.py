import requests as r

reqdict = {"query":{"area"}}

json_dict = json.dumps(reqdict)

myreq = r.post('https://stg-api.openbeta.io', 
               data = { 'Content-Type': 'application/json' 
               'body':'''{"query"{"area"("uuid":"c441f90b-7951-5ff1-b10a-e559fe24d2bc"){"id""totalClimbs"}}'''
               }
            )