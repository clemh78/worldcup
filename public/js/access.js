userRoles = {
    public: 1000,
    user:   1,
    admin:  2
};

accessLevels = {
    public: userRoles.public |
        userRoles.user   |
        userRoles.admin,
    anon:   userRoles.public,
    user:   userRoles.user |
        userRoles.admin,
    admin:  userRoles.admin
};