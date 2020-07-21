bold=$(tput bold)
red=$(tput setaf 1)
green=$(tput setaf 2)
normal=$(tput sgr0)
RESULTS=""
HAS_FAILED_TESTS=0

function run() {
    PHP_VERSION=$1
    PDNS_VERSION=$2
    SHORT_PDNS=${PDNS_VERSION//./}

    echo ""
    echo "--------------------------------------------"
    echo "Testing with PHP ${bold}$PHP_VERSION${normal} and PowerDNS ${bold}$PDNS_VERSION${normal}"
    echo "--------------------------------------------"

    docker run \
        -it \
        --rm \
        --name php"$PHP_VERSION" \
        --link pdns"$SHORT_PDNS":pdns \
        -e PDNS_HOST="http://pdns" \
        --net powerdns-php_default \
        -v "$PWD":/usr/src \
        -w /usr/src/ \
        php:"$PHP_VERSION"-cli \
        php ./vendor/bin/phpunit

    if [ $? -eq 0 ]; then
        RESULTS="$RESULTS\n${green}✓ PHP $PHP_VERSION / PDNS: $PDNS_VERSION"
    else
        HAS_FAILED_TESTS=1
        RESULTS="$RESULTS\n${red}𐄂 PHP $PHP_VERSION / PDNS: $PDNS_VERSION${normal}"
    fi

    echo "(PHP $PHP_VERSION / PDNS: $PDNS_VERSION)"
}

# Get the arguments from the call (i.e. ./run-tests.sh 7.4 4.3)
SET_PHP_VERSION=$1
SET_PDNS_VERSION=$2

# If both arguments are given, only run that combo.
if [ "$#" -eq 2 ]; then
    run "$SET_PHP_VERSION" "$SET_PDNS_VERSION"
else
    # Run tests for all supported PHP 7 / PowerDNS 4 combinations.
    for phpversion in {1..4}; do
        for pdnsversion in {1..3}; do
            run "7.$phpversion" "4.$pdnsversion"
        done
        RESULTS="$RESULTS\n"
    done
fi

echo "\n\nRESULTS"
echo "----------------------"
echo $RESULTS

exit $HAS_FAILED_TESTS
