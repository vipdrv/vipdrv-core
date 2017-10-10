import { Routes, RouterModule }  from '@angular/router';
import { Pages } from './pages.component';
import { ModuleWithProviders } from '@angular/core';
import { AuthorizationManager } from './../utils/index';

// noinspection TypeScriptValidateTypes

// export function loadChildren(path) { return System.import(path); };

export const routes: Routes = [
    {
        path: 'login',
        loadChildren: './login/login.module#LoginModule'
    },
    {
        path: 'logout',
        loadChildren: './logout/logout.module#LogoutModule'
    },
    {
        path: 'pages',
        component: Pages,
        children: [
            { path: '', redirectTo: 'home', pathMatch: 'full', canActivate: [AuthorizationManager] },
            { path: 'home', loadChildren: './home/home.module#HomeModule', canActivate: [AuthorizationManager] },
            {
                path: 'integration',
                loadChildren: './integration/integration.module#IntegrationModule',
                canActivate: [AuthorizationManager]
            },
            { path: 'leads', loadChildren: './leads/leads.module#LeadsModule', canActivate: [AuthorizationManager] },
            { path: 'sites', loadChildren: './sites/sites.module#SitesModule', canActivate: [AuthorizationManager] },
            { path: 'test', loadChildren: './test/test.module#TestModule', canActivate: [AuthorizationManager] }
        ]
    }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);
