import { Routes, RouterModule }  from '@angular/router';
import { Pages } from './pages.component';
import { ModuleWithProviders } from '@angular/core';
// noinspection TypeScriptValidateTypes

// export function loadChildren(path) { return System.import(path); };

export const routes: Routes = [
  {
    path: 'pages',
    component: Pages,
    children: [
      { path: '', redirectTo: 'home', pathMatch: 'full' },
      { path: 'home', loadChildren: './home/home.module#HomeModule' },
      { path: 'integration', loadChildren: './integration/integration.module#IntegrationModule' },
      { path: 'leads', loadChildren: './leads/leads.module#LeadsModule' },
      { path: 'sites', loadChildren: './sites/sites.module#SitesModule' },
      { path: 'test', loadChildren: './test/test.module#TestModule' },
    ]
  }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);
