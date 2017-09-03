import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { SitesComponent } from './sites.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: SitesComponent,
        children: []
    }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);