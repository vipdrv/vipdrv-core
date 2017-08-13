import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { Test } from './test.component';
import { routing } from './test.routing';

import { BtnViewer } from './btnViewer';
import { BootstrapBtnMessageService } from './btnViewer/bootstrapBtnMessage.service';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing
    ],
    declarations: [
        Test,
        BtnViewer
    ],
    providers: [
        BootstrapBtnMessageService
    ]
})
export class TestModule {}
