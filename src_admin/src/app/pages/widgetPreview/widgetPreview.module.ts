import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { BusyModule } from 'angular2-busy';
import { UtilsModule } from './../../utils/index';
import { routing } from './widgetPreview.routing';
import { WidgetPreviewComponent } from './widgetPreview.component';
@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule,
        Ng2Bs3ModalModule,
        BusyModule,
    ],
    exports: [
        WidgetPreviewComponent,
    ],
    declarations: [
        WidgetPreviewComponent,
    ],
    providers: []
})
export class WidgetPreviewModule {}
