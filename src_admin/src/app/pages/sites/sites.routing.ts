import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { SitesComponent } from './sites.component';
import { SiteDetailsRelationsComponent } from './details/relations/siteDetailsRelations.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    {
        path: '',
        component: SitesComponent,
        children: []
    },
    {
        path: ':entityId',
        component: SiteDetailsRelationsComponent,
        children: []
    },
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);