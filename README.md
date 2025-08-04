Im Ordner mit Dockerfile:
```
docker build -t dateiweb .
```

Starten:
```
docker run -it --rm -p 5000:5000 -v "$PWD/uploads:/var/www/html/uploads" dateiweb
```