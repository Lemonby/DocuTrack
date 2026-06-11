import re
import base64

with open("assets/images/icon/laptop-about.svg", "r") as f:
    content = f.read()

# Find the first base64 png
match = re.search(r'data:image/png;base64,([^"]+)', content)
if match:
    img_data = base64.b64decode(match.group(1))
    with open("assets/images/icon/laptop-about.png", "wb") as f:
        f.write(img_data)
    print("Extracted laptop-about.png successfully.")
else:
    print("Could not find base64 image in SVG.")
