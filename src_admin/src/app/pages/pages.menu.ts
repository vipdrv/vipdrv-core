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
                    order: 0,
                    requiredPermission: 'CanViewMenuHome'
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
                    order: 1,
                    requiredPermission: 'CanViewMenuSites'
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
                    order: 2,
                    requiredPermission: 'CanViewMenuLeads'
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
                    order: 3,
                    requiredPermission: 'CanViewMenuSettings'
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
                            order: 0,
                            requiredPermission: 'CanViewMenuInvitations'
                        }
                    }
                },

            ]
        }
    ]
}];

// {
//     path: 'users',
//         data: {
//     menu: {
//         title: 'general.menu.users',
//             icon: 'ion-android-folder-open',
//             selected: false,
//             expanded: false,
//             order: 0,
//             requiredPermission: 'CanViewMenuInvitations'
//     }
// }
// }

