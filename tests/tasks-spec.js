describe('Tasks Tab', function() {

    beforeEach(function() {
        browser.get('http://localhost:9001/tasks');
    });

    it('needs to login', function() {

        var l = element(by.linkText('Sign In')).click();

        element(by.id('email')).sendKeys('protractor@example.com');
        element(by.id('password')).sendKeys('default');

        element(by.id('sign-in-btn')).click();

        var mis = element.all(by.css('#menu .ng-hide'));
        expect(mis.count()).toEqual(2);

    });

    it('should add a task', function() {

        var message = "Test should pass";

        var mi = element(by.id('title'));

        mi.sendKeys(message);

        element(by.id('save-btn')).click();

        var item = element(by.css('.panel:nth-child(1)'));

        expect(item.getText()).toContain(message);

    });

    it('should dismiss a task', function() {

        element(by.css('.panel:nth-child(1) button.btn-danger')).click();

        expect(element(by.css('.alert-info > div')).getText()).toEqual('Task dismissed');
        element(by.linkText('Sign Out')).click();
        expect(element(by.css('.btn-primary')).getText()).toEqual('Sign In');

    });

});