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
          path: 'integration',
          data: {
              menu: {
                  title: 'general.menu.integration',
                  icon: 'ion-gear-a',
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
                  order: 1
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
      }
    ]
  }
];
