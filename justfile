phar     := justfile_directory() + '/bin/blob-common.phar'
phar_dir := justfile_directory() + '/tbd'


# Extract Phar.
@phar-extract:
	[ ! -d "{{ phar_dir }}" ] || rm -rf "{{ phar_dir }}"
	php -r '$phar = new Phar("{{ phar }}"); $phar->extractTo("{{ phar_dir }}");'


# Test Phar.
@test:
	phpunit \
		-c "{{ justfile_directory() }}/phpunit-phar.xml.dist" \
		--display-deprecations
