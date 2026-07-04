import sys
from rembg import remove

inp = sys.argv[1]
out = sys.argv[2]

with open(inp, 'rb') as f:
    data = f.read()

result = remove(data)

with open(out, 'wb') as f:
    f.write(result)