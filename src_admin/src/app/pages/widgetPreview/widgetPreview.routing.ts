import { Routes, RouterModule } from '@angular/router';
import { ModuleWithProviders } from '@angular/core';

import { WidgetPreviewComponent } from './widgetPreview.component';
// noinspection TypeScriptValidateTypes
export const routes: Routes = [
    // TODO: remove direct URL access
    // {
    //     path: '',
    //     component: WidgetPreviewComponent,
    //     children: []
    // }
];
export const routing: ModuleWithProviders = RouterModule.forChild(routes);
