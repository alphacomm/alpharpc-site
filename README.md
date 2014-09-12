# How to use

## Generate the docs from alpharpc.

```bash
git clone https://github.com/alphacomm/alpharpc-site.git &&
cd alpharpc-site &&
composer install --no-dev &&
vendor/bin/sculpin alpharpc:fetch &&
vendor/bin/sculpin generate
```

## Publish to alpharpc.net.

```bash
rm -rf build
git clone --branch gh-pages --single-branch https://github.com/alphacomm/alpharpc.git build &&
cp -R output_dev/* build/ &&
cd build &&
git add . &&
git commit &&
git push origin gh-pages &&
cd ..
```
