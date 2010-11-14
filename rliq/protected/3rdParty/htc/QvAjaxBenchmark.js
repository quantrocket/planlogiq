// Build $BuildVersion$

Qva.Benchmark = function (elem) {
    this.Scan = new Qva.Benchmark.Timer ('Scan');
    this.Load = new Qva.Benchmark.Timer ('Load');
    this.Paint = new Qva.Benchmark.Timer ('Paint');
    this.UpdateComplete = new Qva.Benchmark.Timer ('UpdateComplete');
    this.Unlock = new Qva.Benchmark.Timer ('Unlock');
    this.Element = (elem != null) ? elem : document.getElementById("Benchmark");
}


Qva.Benchmark.Timer = function(kind) {
    this.Kind = kind;
}

Qva.Benchmark.Timer.prototype.Start = function() {
    this.Time = new Date ().valueOf ();
}

Qva.Benchmark.Timer.prototype.Stop = function() {
    this.Time = new Date ().valueOf () - this.Time;
}

Qva.Benchmark.Timer.prototype.Text = function () {
    return this.Kind + ': ' + (this.Time / 1000);
}

Qva.Benchmark.prototype.Display = function () {
    var text = '';
    text += this.Load.Text () + ', ';
    text += this.Paint.Text () + ', ';
    text += this.Unlock.Text () + ', ';
    text += this.UpdateComplete.Text ();
    if (this.Element) {
        this.Element.innerText = text;
    } else {
        alert(text);
    }
}
