export const PAGES_MENU = [
  {
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
        path: 'test',
        data: {
            menu: {
                title: 'general.menu.test',
                icon: 'ion-gear-a',
                selected: false,
                expanded: false,
                order: 1
            }
        }
      }
    ]
  }
];
