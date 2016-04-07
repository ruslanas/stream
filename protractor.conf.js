exports.config = {
    seleniumAddress: 'http://localhost:4444/wd/hub',
    specs: [
        'tests/user-spec.js',
        'tests/tasks-spec.js'
    ]
};
