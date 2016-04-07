describe('Home page', function() {

    beforeEach(function() {
        browser.get('http://localhost:9001');
    });

    it('should have a huge button', function() {

        var item = element(by.css('.btn-lg'));
        expect(item.getText()).toContain('Join');

        item.click();
        expect(element(by.css('.btn-primary')).getText()).toEqual('Register');

    });
});
