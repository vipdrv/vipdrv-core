export const PAGES_MENU = [{
    path: 'pages',
    children: [
        {
            path: 'home',
            data: {
                menu: {
                    title: 'general.menu.home',
                    icon: 'ion-android-home',
                    selected: false,
                    expanded: false,
                    order: 0
                }
            }
        },
        {
            path: 'sites',
            data: {
                menu: {
                    title: 'general.menu.sites',
                    icon: 'ion-ios-list-outline',
                    selected: false,
                    expanded: false,
                    order: 1
                }
            }
        },
        {
            path: 'leads',
            data: {
                menu: {
                    title: 'general.menu.leads',
                    icon: 'ion-ios-paper-outline',
                    selected: false,
                    expanded: false,
                    order: 2
                }
            }
        },
        {
            path: 'settings',
            data: {
                menu: {
                    title: 'general.menu.settings',
                    icon: 'ion-settings',
                    selected: false,
                    expanded: false,
                    order: 3
                }
            },
            children: [
                {
                    path: 'invitations',
                    data: {
                        menu: {
                            title: 'general.menu.invitations',
                            // icon: 'ion-ios-filing-outline',
                            icon: 'ion-android-folder-open',
                            selected: false,
                            expanded: false,
                            order: 0
                        }
                    }
                }
            ]
        }
    ]
}];
