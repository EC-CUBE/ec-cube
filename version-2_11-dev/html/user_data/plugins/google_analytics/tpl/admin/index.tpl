<form method="post" action="?">
    <div id="system" class="contents-main">
        <table class="list">
            <tr>
                <th>ウェブ プロパティ ID</th>
                <td>UA-<input type="text" name="ga_ua" value="<!--{$smarty.const.GA_UA}-->" /></td>
            </tr>
        </table>
        <div class="btn addnew">
            <input type="hidden" name="mode" value="register" />
            <button type="submit"><span>この内容で登録する</span></button>
        </div>
    </div>
</form>
