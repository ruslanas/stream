describe('Tasks Tab', function() {

    beforeEach(function() {
        browser.get('http://localhost:9001/tasks');
    });

    it('needs to login', function() {
        
        var l = element(by.linkText('Sign In')).click();

        element(by.name('email')).sendKeys('admin@example.com');
        element(by.name('password')).sendKeys('foo');
        element(by.id('sign-in-btn')).click();
    
        var mis = element.all(by.css('#menu .ng-hide'));
        expect(mis.count()).toEqual(2);

    });

    it('should add a task', function() {
        
        var mi = element(by.id('title'));
        
        mi.sendKeys('New task will apear in database');

        element(by.id('save-btn')).click();

        var item = element(by.css('.panel'));
        
        expect(item.getText()).toContain('New task will apear in database');

    });
});
